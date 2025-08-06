<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use ZipArchive;

class CreateBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:create {--type=all : Тип бэкапа (database, files, all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание резервных копий базы данных и файлов';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        
        $this->info('Начинаем создание резервных копий...');
        
        if ($type === 'all' || $type === 'database') {
            $this->createDatabaseBackup();
        }
        
        if ($type === 'all' || $type === 'files') {
            $this->createFilesBackup();
        }
        
        $this->cleanupOldBackups();
        
        $this->info('Резервное копирование завершено!');
    }
    
    private function createDatabaseBackup()
    {
        $this->info('Создание резервной копии базы данных...');
        
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
                $this->info('Пробуем mysqldump...');
                $success = $this->createBackupWithMysqldump($path);
            }
            
            // Способ 2: Laravel DB (если mysqldump недоступен)
            if (!$success) {
                $this->info('mysqldump недоступен, используем Laravel DB...');
                $success = $this->createBackupWithLaravel($path);
            }
            
            if ($success) {
                // Сжимаем файл
                $this->compressFile($path);
                $this->info("✅ Резервная копия базы данных создана: {$filename}.gz");
            } else {
                $this->error("❌ Не удалось создать резервную копию базы данных");
            }
        } catch (\Exception $e) {
            $this->error("❌ Ошибка: " . $e->getMessage());
        }
    }
    
    private function createFilesBackup()
    {
        $this->info('Создание резервной копии файлов...');
        
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
                $this->info('Пробуем ZIP архив...');
                $success = $this->createBackupWithZip($backupPath);
            }
            
            // Способ 2: TAR.GZ архив (если ZIP недоступен)
            if (!$success) {
                $this->info('ZIP недоступен, пробуем TAR.GZ...');
                $success = $this->createBackupWithTar($backupPath);
            }
            
            // Способ 3: Простое копирование важных файлов
            if (!$success) {
                $this->info('Архивы недоступны, копируем важные файлы...');
                $success = $this->createBackupWithCopy($backupPath);
            }
            
            if ($success) {
                $this->info("✅ Резервная копия файлов создана: {$filename}");
            } else {
                $this->error("❌ Не удалось создать резервную копию файлов");
            }
        } catch (\Exception $e) {
            $this->error("❌ Ошибка: " . $e->getMessage());
        }
    }
    
    private function cleanupOldBackups()
    {
        $this->info('Очистка старых резервных копий...');
        
        // Удаляем бэкапы старше 30 дней
        $cutoffDate = Carbon::now()->subDays(30);
        
        $backupTypes = ['database', 'files'];
        
        foreach ($backupTypes as $type) {
            $backupPath = storage_path("backups/{$type}");
            
            if (File::exists($backupPath)) {
                $files = File::files($backupPath);
                $deletedCount = 0;
                
                foreach ($files as $file) {
                    $fileDate = Carbon::createFromTimestamp($file->getMTime());
                    
                    if ($fileDate->lt($cutoffDate)) {
                        File::delete($file->getPathname());
                        $deletedCount++;
                    }
                }
                
                if ($deletedCount > 0) {
                    $this->info("🗑️ Удалено {$deletedCount} старых файлов из {$type}");
                }
            }
        }
    }
    
    private function compressFile($path)
    {
        $gzPath = $path . '.gz';
        $content = file_get_contents($path);
        file_put_contents($gzPath, gzencode($content, 9));
        File::delete($path); // Удаляем оригинальный файл
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
                // Добавляем файлы проекта (исключаем node_modules, vendor, .git)
                $this->addFolderToZip($zip, base_path(), [
                    'node_modules',
                    'vendor',
                    '.git',
                    'storage/logs',
                    'storage/framework/cache',
                    'storage/framework/sessions',
                    'storage/framework/views',
                    'storage/backups'
                ]);
                
                if ($zip->close()) {
                    // Переименовываем обратно в .tar.gz для единообразия
                    File::move($backupPath, str_replace('.zip', '.tar.gz', $backupPath));
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
                        if (!File::exists(dirname($destPath))) {
                            File::makeDirectory(dirname($destPath), 0755, true);
                        }
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
} 