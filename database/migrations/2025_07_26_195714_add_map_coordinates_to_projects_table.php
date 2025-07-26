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
            $table->string('map_latitude')->nullable()->after('address');
            $table->string('map_longitude')->nullable()->after('map_latitude');
            $table->string('map_zoom')->default('15')->after('map_longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['map_latitude', 'map_longitude', 'map_zoom']);
        });
    }
};
