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
        Schema::create('salary_calculations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('services_count')->default(0);
            $table->decimal('services_amount', 10, 2)->default(0);
            $table->integer('sales_count')->default(0);
            $table->decimal('sales_amount', 10, 2)->default(0);
            $table->decimal('fixed_salary', 10, 2)->default(0);
            $table->decimal('percentage_salary', 10, 2)->default(0);
            $table->decimal('bonuses', 10, 2)->default(0);
            $table->decimal('penalties', 10, 2)->default(0);
            $table->decimal('total_salary', 10, 2)->default(0);
            $table->enum('status', ['calculated', 'approved', 'paid'])->default('calculated');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('admin_users')->onDelete('cascade');
            $table->index(['project_id', 'user_id', 'period_start'], 'salary_calc_project_user_period_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_calculations');
    }
};
