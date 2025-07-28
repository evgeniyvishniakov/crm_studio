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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('email_host')->nullable()->after('telegram_notifications_enabled');
            $table->integer('email_port')->nullable()->after('email_host');
            $table->string('email_username')->nullable()->after('email_port');
            $table->string('email_password')->nullable()->after('email_username');
            $table->enum('email_encryption', ['tls', 'ssl', 'none'])->default('tls')->after('email_password');
            $table->string('email_from_name')->nullable()->after('email_encryption');
            $table->boolean('email_notifications_enabled')->default(false)->after('email_from_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'email_host',
                'email_port',
                'email_username',
                'email_password',
                'email_encryption',
                'email_from_name',
                'email_notifications_enabled'
            ]);
        });
    }
};
