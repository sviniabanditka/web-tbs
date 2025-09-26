<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('turn_id')->nullable()->constrained('game_turns')->onDelete('cascade');
            $table->foreignId('player_id')->constrained('users')->onDelete('cascade');
            $table->string('action_type');
            $table->json('action_data');
            $table->enum('status', ['pending', 'executed', 'failed'])->default('pending');
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_actions');
    }
};
