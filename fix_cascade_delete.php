<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Проверка и исправление каскадного удаления для проектов ===\n\n";

try {
    // Получаем все таблицы с project_id
    $tables = DB::select('SHOW TABLES');
    $databaseName = DB::connection()->getDatabaseName();
    
    $tablesWithProjectId = [];
    
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        
        // Проверяем, есть ли поле project_id в таблице
        $columns = DB::select("SHOW COLUMNS FROM `{$tableName}` LIKE 'project_id'");
        
        if (!empty($columns)) {
            $tablesWithProjectId[] = $tableName;
        }
    }
    
    echo "Найдены таблицы с project_id:\n";
    foreach ($tablesWithProjectId as $table) {
        echo "- {$table}\n";
    }
    
    echo "\n=== Проверка внешних ключей ===\n";
    
    $tablesWithoutForeignKey = [];
    
    foreach ($tablesWithProjectId as $tableName) {
        // Проверяем, есть ли внешний ключ
        $foreignKeys = DB::select("
            SELECT 
                CONSTRAINT_NAME,
                DELETE_RULE
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = ? 
            AND COLUMN_NAME = 'project_id'
            AND REFERENCED_TABLE_NAME = 'projects'
        ", [$databaseName, $tableName]);
        
        if (!empty($foreignKeys)) {
            $fk = $foreignKeys[0];
            echo "✓ {$tableName} - {$fk->CONSTRAINT_NAME} (ON DELETE {$fk->DELETE_RULE})\n";
        } else {
            echo "❌ {$tableName} - НЕТ ВНЕШНЕГО КЛЮЧА\n";
            $tablesWithoutForeignKey[] = $tableName;
        }
    }
    
    if (!empty($tablesWithoutForeignKey)) {
        echo "\n=== Добавление внешних ключей ===\n";
        
        foreach ($tablesWithoutForeignKey as $tableName) {
            try {
                echo "Добавляем внешний ключ для {$tableName}... ";
                
                DB::statement("
                    ALTER TABLE `{$tableName}` 
                    ADD CONSTRAINT `fk_{$tableName}_project_id` 
                    FOREIGN KEY (`project_id`) 
                    REFERENCES `projects`(`id`) 
                    ON DELETE CASCADE
                ");
                
                echo "✓ Успешно\n";
            } catch (Exception $e) {
                echo "❌ Ошибка: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "\n✓ Все таблицы уже имеют внешние ключи с каскадным удалением!\n";
    }
    
    echo "\n=== Финальная проверка ===\n";
    
    foreach ($tablesWithProjectId as $tableName) {
        $foreignKeys = DB::select("
            SELECT 
                CONSTRAINT_NAME,
                DELETE_RULE
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = ? 
            AND COLUMN_NAME = 'project_id'
            AND REFERENCED_TABLE_NAME = 'projects'
        ", [$databaseName, $tableName]);
        
        if (!empty($foreignKeys)) {
            $fk = $foreignKeys[0];
            echo "✓ {$tableName} - {$fk->CONSTRAINT_NAME} (ON DELETE {$fk->DELETE_RULE})\n";
        } else {
            echo "❌ {$tableName} - ПРОБЛЕМА НЕ РЕШЕНА\n";
        }
    }
    
    echo "\n=== Готово! ===\n";
    echo "Теперь при удалении проекта все связанные записи будут удалены автоматически.\n";
    
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}
