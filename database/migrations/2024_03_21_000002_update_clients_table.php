<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            // Удаляем старое поле status
            $table->dropColumn('status');
            
            // Добавляем связь с типами клиентов
            $table->foreignId('client_type_id')->nullable()->constrained('client_types');
            
            // Добавляем дополнительные поля
            $table->text('notes')->nullable();
            $table->date('birth_date')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            // Удаляем новые поля
            $table->dropForeign(['client_type_id']);
            $table->dropColumn(['client_type_id', 'notes', 'birth_date', 'is_active']);
            
            // Возвращаем старое поле status
            $table->string('status')->default('new');
        });
    }
}; 