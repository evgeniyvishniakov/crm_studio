<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Добавляем внешний ключ для связи с таблицей поставщиков
            $table->foreignId('supplier_id')->nullable()->after('id')->constrained('suppliers')->nullOnDelete();
            
            // Удаляем старое текстовое поле supplier
            $table->dropColumn('supplier');
        });
    }

    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Возвращаем старое текстовое поле
            $table->string('supplier')->nullable();
            
            // Удаляем внешний ключ
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });
    }
}; 