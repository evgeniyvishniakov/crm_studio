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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('CRM Studio');
            $table->text('site_description')->nullable();
            $table->string('admin_email')->nullable();
            $table->string('timezone')->default('Europe/Moscow');
            $table->string('landing_logo')->nullable(); // Путь к логотипу лендинга
            $table->string('favicon')->nullable(); // Путь к фавикону
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};



