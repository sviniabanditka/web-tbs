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
        Schema::create('game_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained('game_players');
            $table->integer('turn_number');
            $table->string('action_type');
            $table->json('action_data')->nullable();
            $table->integer('action_points_cost');
            $table->foreignId('source_hex_id')->nullable()->constrained('game_hexes');
            $table->foreignId('target_hex_id')->nullable()->constrained('game_hexes');
            $table->foreignId('unit_id')->nullable()->constrained('units');
            $table->foreignId('building_id')->nullable()->constrained('buildings');
            $table->boolean('successful');
            $table->text('error_message')->nullable();
            $table->timestamp('executed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_actions');
    }
};
