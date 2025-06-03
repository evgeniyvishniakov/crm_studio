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
        Schema::table('appointments', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
        });
    }
};
