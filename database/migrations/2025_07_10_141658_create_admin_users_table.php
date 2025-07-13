<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Имя
            $table->string('email')->unique(); // Email
            $table->string('password')->nullable(); // Пароль
            $table->unsignedBigInteger('project_id'); // Проект
            $table->string('role'); // Роль
            $table->string('status')->default('active'); // Статус
            $table->timestamp('registered_at')->useCurrent(); // Дата регистрации
            $table->timestamp('email_verified_at')->nullable(); // Дата подтверждения email
            $table->rememberToken(); // Remember token для аутентификации
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->index(['project_id', 'role', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users');
    }
};
