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
        Schema::create('admin_telegram_settings', function (Blueprint $table) {
            $table->id();
            $table->string('telegram_bot_token')->nullable();
            $table->string('telegram_chat_id')->nullable();
            $table->boolean('telegram_notifications_enabled')->default(false);
            $table->boolean('notify_new_projects')->default(true);
            $table->boolean('notify_new_subscriptions')->default(true);
            $table->boolean('notify_new_messages')->default(true);
            $table->boolean('notify_subscription_expires')->default(true);
            $table->boolean('notify_payment_issues')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_telegram_settings');
    }
};
