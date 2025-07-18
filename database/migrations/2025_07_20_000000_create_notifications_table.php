<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // null = для всех
            $table->string('type', 32); // ticket, user, system, etc
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('admin_users')->onDelete('set null');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
}; 