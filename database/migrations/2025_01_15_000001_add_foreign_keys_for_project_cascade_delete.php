<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Добавляем внешний ключ для таблицы employee_time_offs
        if (Schema::hasTable('employee_time_offs') && Schema::hasColumn('employee_time_offs', 'project_id')) {
            // Проверяем, есть ли уже внешний ключ
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'employee_time_offs' 
                AND COLUMN_NAME = 'project_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (empty($foreignKeys)) {
                Schema::table('employee_time_offs', function (Blueprint $table) {
                    $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
                });
            }
        }

        // Добавляем внешний ключ для таблицы clients
        if (Schema::hasTable('clients') && Schema::hasColumn('clients', 'project_id')) {
            // Проверяем, есть ли уже внешний ключ
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'clients' 
                AND COLUMN_NAME = 'project_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (empty($foreignKeys)) {
                Schema::table('clients', function (Blueprint $table) {
                    $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
                });
            }
        }

        // Проверяем и добавляем внешние ключи для других таблиц, которые могли быть созданы без миграций
        $tablesToCheck = [
            'services',
            'products', 
            'purchases',
            'sales',
            'expenses',
            'warehouse',
            'notifications',
            'user_schedules',
            'blog_articles',
            'blog_categories',
            'knowledge_articles',
            'knowledge_article_steps',
            'knowledge_article_tips',
            'knowledge_article_translations',
            'knowledge_article_step_translations',
            'knowledge_article_tip_translations'
        ];

        foreach ($tablesToCheck as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'project_id')) {
                // Проверяем, есть ли уже внешний ключ
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = ? 
                    AND COLUMN_NAME = 'project_id'
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ", [$tableName]);
                
                if (empty($foreignKeys)) {
                    try {
                        Schema::table($tableName, function (Blueprint $table) {
                            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
                        });
                    } catch (Exception $e) {
                        // Игнорируем ошибки, если внешний ключ уже существует или есть другие проблемы
                        echo "Warning: Could not add foreign key to {$tableName}: " . $e->getMessage() . "\n";
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем внешние ключи в обратном порядке
        $tables = [
            'employee_time_offs',
            'clients',
            'services',
            'products', 
            'purchases',
            'sales',
            'expenses',
            'warehouse',
            'notifications',
            'user_schedules',
            'blog_articles',
            'blog_categories',
            'knowledge_articles',
            'knowledge_article_steps',
            'knowledge_article_tips',
            'knowledge_article_translations',
            'knowledge_article_step_translations',
            'knowledge_article_tip_translations'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                try {
                    // Получаем имя внешнего ключа
                    $foreignKeys = DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM information_schema.KEY_COLUMN_USAGE 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = ? 
                        AND COLUMN_NAME = 'project_id'
                        AND REFERENCED_TABLE_NAME = 'projects'
                    ", [$tableName]);
                    
                    foreach ($foreignKeys as $fk) {
                        Schema::table($tableName, function (Blueprint $table) use ($fk) {
                            $table->dropForeign([$fk->CONSTRAINT_NAME]);
                        });
                    }
                } catch (Exception $e) {
                    // Игнорируем ошибки
                }
            }
        }
    }
};
