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
        Schema::table('game_players', function (Blueprint $table) {
            $table->unsignedInteger('gold')->default(100)->after('is_ready');
            $table->unsignedInteger('food')->default(100)->after('gold');
            $table->unsignedInteger('wood')->default(50)->after('food');
            $table->unsignedInteger('stone')->default(50)->after('wood');
            $table->unsignedInteger('iron')->default(0)->after('stone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_players', function (Blueprint $table) {
            $table->dropColumn(['gold', 'food', 'wood', 'stone', 'iron']);
        });
    }
};
