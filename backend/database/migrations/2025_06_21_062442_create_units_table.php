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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained('game_players');
            $table->foreignId('hex_id')->constrained('game_hexes');
            $table->string('type');
            $table->string('name');
            $table->integer('level')->default(1);
            $table->integer('health');
            $table->integer('max_health');
            $table->integer('attack');
            $table->integer('defense');
            $table->integer('movement_range');
            $table->integer('movement_points');
            $table->integer('max_movement_points');
            $table->integer('experience')->default(0);
            $table->boolean('is_fortified')->default(false);
            $table->integer('fortified_turns')->default(0);
            $table->timestamp('destroyed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
