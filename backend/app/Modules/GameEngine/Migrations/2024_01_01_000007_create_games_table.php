<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['waiting', 'active', 'finished', 'paused'])->default('waiting');
            $table->integer('current_turn')->default(1);
            $table->foreignId('current_player_id')->nullable()->constrained('users');
            $table->json('game_data')->nullable();
            $table->json('map_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
