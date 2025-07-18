<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('support_ticket_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->text('message');
            $table->boolean('is_admin')->default(false);
            $table->timestamps();

            $table->foreign('support_ticket_id')->references('id')->on('support_tickets')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('admin_users')->onDelete('set null');
            $table->foreign('admin_id')->references('id')->on('admin_users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_messages');
    }
}; 