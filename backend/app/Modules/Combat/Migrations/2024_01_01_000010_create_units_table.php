<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained('users')->onDelete('cascade');
            $table->string('unit_type');
            $table->integer('position_q');
            $table->integer('position_r');
            $table->integer('health');
            $table->integer('max_health');
            $table->integer('attack');
            $table->integer('defense');
            $table->integer('movement');
            $table->enum('status', ['active', 'wounded', 'dead'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
