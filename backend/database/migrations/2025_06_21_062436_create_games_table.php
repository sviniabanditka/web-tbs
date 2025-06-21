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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('waiting');
            $table->integer('current_turn')->default(0);
            $table->integer('max_players');
            $table->integer('turn_time_limit')->nullable();
            $table->integer('action_points_per_turn');
            $table->unsignedInteger('map_generation_seed');
            $table->string('map_generation_algorithm');
            $table->integer('map_size');
            $table->json('terrain_parameters')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
