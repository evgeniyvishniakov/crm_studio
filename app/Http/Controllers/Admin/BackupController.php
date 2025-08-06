<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use ZipArchive;

class BackupController extends Controller
{
    public function index()
    {
        $backups = $this->getBackupsList();
        $diskUsage = $this->getDiskUsage();
        $lastBackup = $this->getLastBackup();
        
        return view('admin.backups.index', compact('backups', 'diskUsage', 'lastBackup'));
    }

    public function createDatabaseBackup()
    {
        try {
            $filename = 'database_backup_' . date('Y_m_d_H_i_s') . '.sql';
            $path = storage_path('backups/database/' . $filename);
            
            // Создаем директорию если не существует
            if (!File::exists(dirname($path))) {
                File::makeDirectory(dirname($path), 0755, true);
            }

            // Пробуем разные способы создания бэкапа
            $success = false;
            
            // Способ 1: mysqldump (если доступен)
            if (!$success) {
                $success = $this->createBackupWithMysqldump($path);
            }
            
            // Способ 2: Laravel DB (если mysqldump недоступен)
            if (!$success) {
                $success = $this->createBackupWithLaravel($path);
            }
            
            if ($success) {
                // Сжимаем файл
                $this->compressFile($path);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Резервная копия базы данных создана успешно',
                    'filename' => $filename . '.gz'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Не удалось создать резервную копию базы данных. Проверьте настройки подключения к БД.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createFilesBackup()
    {
        try {
            $filename = 'files_backup_' . date('Y_m_d_H_i_s') . '.tar.gz';
            $backupPath = storage_path('backups/files/' . $filename);
            
            // Создаем директорию если не существует
            if (!File::exists(dirname($backupPath))) {
                File::makeDirectory(dirname($backupPath), 0755, true);
            }

            // Пробуем разные способы создания бэкапа файлов
            $success = false;
            
            // Способ 1: ZIP архив (если доступен)
            if (!$success) {
                $success = $this->createBackupWithZip($backupPath);
            }
            
            // Способ 2: TAR.GZ архив (если ZIP недоступен)
            if (!$success) {
                $success = $this->createBackupWithTar($backupPath);
            }
            
            // Способ 3: Простое копирование важных файлов
            if (!$success) {
                $success = $this->createBackupWithCopy($backupPath);
            }
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Резервная копия файлов создана успешно',
                    'filename' => $filename
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Не удалось создать резервную копию файлов. Проверьте права доступа.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadBackup($type, $filename)
    {
        $path = storage_path("backups/{$type}/{$filename}");
        
        if (File::exists($path)) {
            return response()->download($path);
        }
        
        return redirect()->back()->with('error', 'Файл не найден');
    }

    public function deleteBackup($type, $filename)
    {
        $path = storage_path("backups/{$type}/{$filename}");
        
        if (File::exists($path)) {
            File::delete($path);
            return response()->json([
                'success' => true,
                'message' => 'Резервная копия удалена'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Файл не найден'
        ], 404);
    }

    public function restoreDatabase(Request $request)
    {
        try {
            $filename = $request->input('filename');
            $path = storage_path("backups/database/{$filename}");
            
            if (!File::exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Файл резервной копии не найден'
                ], 404);
            }

            // Распаковываем если файл сжат
            if (pathinfo($path, PATHINFO_EXTENSION) === 'gz') {
                $uncompressedPath = $this->decompressFile($path);
            } else {
                $uncompressedPath = $path;
            }

            // Получаем настройки БД
            $host = config('database.connections.mysql.host');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // Команда для восстановления
            $command = "mysql -h {$host} -u {$username}";
            if ($password) {
                $command .= " -p{$password}";
            }
            $command .= " {$database} < {$uncompressedPath}";

            exec($command, $output, $returnCode);

            // Удаляем временный файл
            if ($uncompressedPath !== $path) {
                File::delete($uncompressedPath);
            }

            if ($returnCode === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'База данных восстановлена успешно'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при восстановлении базы данных'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getBackupsList()
    {
        $backups = [];
        
        // Базы данных
        $dbPath = storage_path('backups/database');
        if (File::exists($dbPath)) {
            $files = File::files($dbPath);
            foreach ($files as $file) {
                $backups['database'][] = [
                    'name' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'date' => Carbon::createFromTimestamp($file->getMTime())->format('d.m.Y H:i:s'),
                    'timestamp' => $file->getMTime()
                ];
            }
        }
        
        // Файлы
        $filesPath = storage_path('backups/files');
        if (File::exists($filesPath)) {
            $files = File::files($filesPath);
            foreach ($files as $file) {
                $backups['files'][] = [
                    'name' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'date' => Carbon::createFromTimestamp($file->getMTime())->format('d.m.Y H:i:s'),
                    'timestamp' => $file->getMTime()
                ];
            }
        }
        
        // Сортируем по дате (новые сначала)
        if (isset($backups['database'])) {
            usort($backups['database'], function($a, $b) {
                return $b['timestamp'] - $a['timestamp'];
            });
        }
        if (isset($backups['files'])) {
            usort($backups['files'], function($a, $b) {
                return $b['timestamp'] - $a['timestamp'];
            });
        }
        
        return $backups;
    }

    private function getDiskUsage()
    {
        $backupPath = storage_path('backups');
        $totalSize = 0;
        
        if (File::exists($backupPath)) {
            $totalSize = $this->getDirectorySize($backupPath);
        }
        
        return [
            'total' => $this->formatBytes($totalSize),
            'total_bytes' => $totalSize
        ];
    }

    private function getLastBackup()
    {
        $backups = $this->getBackupsList();
        $lastBackup = null;
        
        foreach (['database', 'files'] as $type) {
            if (isset($backups[$type]) && !empty($backups[$type])) {
                $backup = $backups[$type][0];
                if (!$lastBackup || $backup['timestamp'] > $lastBackup['timestamp']) {
                    $lastBackup = $backup;
                    $lastBackup['type'] = $type;
                }
            }
        }
        
        return $lastBackup;
    }

    private function compressFile($path)
    {
        $gzPath = $path . '.gz';
        $content = file_get_contents($path);
        file_put_contents($gzPath, gzencode($content, 9));
        File::delete($path); // Удаляем оригинальный файл
    }

    private function decompressFile($path)
    {
        $content = file_get_contents($path);
        $uncompressed = gzdecode($content);
        $tempPath = $path . '.tmp';
        file_put_contents($tempPath, $uncompressed);
        return $tempPath;
    }

    private function addFolderToZip($zip, $folder, $exclude = [])
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folder),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($folder) + 1);
                
                // Проверяем исключения
                $excludeFile = false;
                foreach ($exclude as $excludePath) {
                    if (strpos($relativePath, $excludePath) === 0) {
                        $excludeFile = true;
                        break;
                    }
                }
                
                if (!$excludeFile) {
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
    }

    private function getDirectorySize($path)
    {
        $size = 0;
        foreach (File::allFiles($path) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }

    private function createBackupWithMysqldump($path)
    {
        try {
            // Получаем настройки БД
            $host = config('database.connections.mysql.host');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // Команда для создания дампа
            $command = "mysqldump -h {$host} -u {$username}";
            if ($password) {
                $command .= " -p{$password}";
            }
            $command .= " {$database} > {$path}";

            exec($command, $output, $returnCode);

            return $returnCode === 0 && File::exists($path);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function createBackupWithLaravel($path)
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $output = '';
            
            // Получаем список таблиц
            $tableNames = [];
            foreach ($tables as $table) {
                $tableNames[] = array_values((array) $table)[0];
            }
            
            // Создаем дамп для каждой таблицы
            foreach ($tableNames as $tableName) {
                $output .= $this->getTableStructure($tableName);
                $output .= $this->getTableData($tableName);
            }
            
            // Записываем в файл
            File::put($path, $output);
            
            return File::exists($path) && File::size($path) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getTableStructure($tableName)
    {
        $output = "\n-- Структура таблицы `{$tableName}`\n";
        $output .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
        
        $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
        $createTableArray = (array) $createTable;
        $createTableSql = array_values($createTableArray)[1];
        
        $output .= $createTableSql . ";\n\n";
        
        return $output;
    }

    private function getTableData($tableName)
    {
        $output = "-- Данные таблицы `{$tableName}`\n";
        
        $rows = DB::table($tableName)->get();
        
        if ($rows->count() > 0) {
            $columns = array_keys((array) $rows->first());
            $output .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES\n";
            
            $values = [];
            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $rowValues = [];
                
                foreach ($rowArray as $value) {
                    if ($value === null) {
                        $rowValues[] = 'NULL';
                    } else {
                        $rowValues[] = "'" . addslashes($value) . "'";
                    }
                }
                
                $values[] = "(" . implode(', ', $rowValues) . ")";
            }
            
            $output .= implode(",\n", $values) . ";\n\n";
        }
        
        return $output;
    }

    private function createBackupWithZip($backupPath)
    {
        try {
            // Меняем расширение на .zip
            $backupPath = str_replace('.tar.gz', '.zip', $backupPath);
            
            $zip = new ZipArchive();
            $result = $zip->open($backupPath, ZipArchive::CREATE);
            
            if ($result === TRUE) {
                // Добавляем файлы проекта (исключаем ненужные папки и файлы)
                $this->addFolderToZip($zip, base_path(), [
                    'node_modules',
                    'vendor',
                    '.git',
                    '.idea',
                    '.cursor',
                    'storage/logs',
                    'storage/framework/cache',
                    'storage/framework/sessions',
                    'storage/framework/views',
                    'storage/backups',
                    'storage/app/public/uploads/temp',
                    'storage/app/public/uploads/cache'
                ]);
                
                if ($zip->close()) {
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function createBackupWithTar($backupPath)
    {
        try {
            // Проверяем доступность tar команды
            exec('tar --version', $output, $returnCode);
            
            if ($returnCode === 0) {
                $excludeArgs = [
                    '--exclude=node_modules',
                    '--exclude=vendor',
                    '--exclude=.git',
                    '--exclude=storage/logs',
                    '--exclude=storage/framework/cache',
                    '--exclude=storage/framework/sessions',
                    '--exclude=storage/framework/views',
                    '--exclude=storage/backups'
                ];
                
                $command = 'tar -czf "' . $backupPath . '" ' . implode(' ', $excludeArgs) . ' -C "' . base_path() . '" .';
                exec($command, $output, $returnCode);
                
                return $returnCode === 0 && File::exists($backupPath);
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function createBackupWithCopy($backupPath)
    {
        try {
            // Создаем простой список важных файлов
            $importantFiles = [
                '.env',
                'composer.json',
                'composer.lock',
                'package.json',
                'package-lock.json',
                'artisan',
                'app/',
                'config/',
                'database/',
                'resources/',
                'routes/',
                'public/',
                'storage/app/',
                'storage/framework/keys/',
                'bootstrap/'
            ];
            
            $backupDir = str_replace('.tar.gz', '_files', $backupPath);
            
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }
            
            $copiedFiles = 0;
            
            foreach ($importantFiles as $file) {
                $sourcePath = base_path($file);
                $destPath = $backupDir . '/' . $file;
                
                if (File::exists($sourcePath)) {
                    if (File::isDirectory($sourcePath)) {
                        $this->copyDirectory($sourcePath, $destPath);
                        $copiedFiles++;
                    } else {
                        File::copyDirectory(dirname($sourcePath), dirname($destPath));
                        File::copy($sourcePath, $destPath);
                        $copiedFiles++;
                    }
                }
            }
            
            // Создаем архив из скопированных файлов
            if ($copiedFiles > 0) {
                $zip = new ZipArchive();
                $zipPath = str_replace('_files', '.zip', $backupDir);
                
                if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                    $this->addFolderToZip($zip, $backupDir, []);
                    $zip->close();
                    
                    // Удаляем временную директорию
                    File::deleteDirectory($backupDir);
                    
                    // Переименовываем в .tar.gz для единообразия
                    File::move($zipPath, $backupPath);
                    
                    return File::exists($backupPath);
                }
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function copyDirectory($source, $destination)
    {
        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }
        
        $files = File::allFiles($source);
        
        foreach ($files as $file) {
            $relativePath = str_replace($source . '/', '', $file->getPathname());
            $destFile = $destination . '/' . $relativePath;
            
            if (!File::exists(dirname($destFile))) {
                File::makeDirectory(dirname($destFile), 0755, true);
            }
            
            File::copy($file->getPathname(), $destFile);
        }
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
} 