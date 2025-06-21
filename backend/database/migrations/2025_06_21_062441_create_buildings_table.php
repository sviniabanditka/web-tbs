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
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained('game_players');
            $table->foreignId('hex_id')->constrained('game_hexes');
            $table->string('type');
            $table->string('name');
            $table->integer('level')->default(1);
            $table->integer('health');
            $table->integer('max_health');
            $table->integer('production_rate');
            $table->integer('storage_capacity');
            $table->integer('defense_bonus');
            $table->boolean('is_capital')->default(false);
            $table->timestamp('constructed_at')->nullable();
            $table->timestamp('destroyed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
