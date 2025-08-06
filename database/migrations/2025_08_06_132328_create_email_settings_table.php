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
        Schema::create('email_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('email_host')->nullable();
            $table->integer('email_port')->nullable();
            $table->string('email_username')->nullable();
            $table->string('email_password')->nullable();
            $table->enum('email_encryption', ['tls', 'ssl', 'none'])->default('tls');
            $table->string('email_from_name')->nullable();
            $table->boolean('email_notifications_enabled')->default(false);
            $table->timestamps();

            // Внешний ключ к таблице projects
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            
            // Уникальный индекс для project_id (один проект - одни настройки email)
            $table->unique('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_settings');
    }
};
