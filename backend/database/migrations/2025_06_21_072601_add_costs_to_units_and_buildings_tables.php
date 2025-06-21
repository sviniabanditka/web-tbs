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
        Schema::table('units', function (Blueprint $table) {
            $table->json('costs')->nullable()->after('max_movement_points');
        });

        Schema::table('buildings', function (Blueprint $table) {
            $table->json('costs')->nullable()->after('is_capital');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('costs');
        });

        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn('costs');
        });
    }
};
