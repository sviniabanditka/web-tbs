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
        Schema::create('game_hexes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->integer('q');
            $table->integer('r');
            $table->string('terrain_type');
            $table->float('elevation');
            $table->float('moisture');
            $table->float('temperature')->nullable();
            $table->string('resource_type')->nullable();
            $table->unsignedInteger('resource_amount')->nullable();
            $table->boolean('is_passable')->default(true);
            $table->unsignedTinyInteger('movement_cost')->default(1);
            $table->integer('defense_bonus')->default(0);
            $table->integer('visibility_bonus')->default(0);
            $table->json('production_bonus')->nullable();
            $table->timestamps();

            $table->unique(['game_id', 'q', 'r']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_hexes');
    }
};
