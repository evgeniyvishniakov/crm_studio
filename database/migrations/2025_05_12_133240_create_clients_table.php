<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Обязательное поле
            $table->string('instagram')->nullable(); // Необязательное
            $table->string('phone')->nullable(); // Необязательное
            $table->string('email')->nullable(); // Необязательное
            $table->string('telegram')->nullable(); // Необязательное
            $table->string('status')->default('new'); // Добавил статус клиента (по умолчанию "new")
            $table->timestamps(); // created_at и updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
};
