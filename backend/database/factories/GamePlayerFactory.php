<?php

namespace Database\Factories;

use App\Enums\PlayerColor;
use App\Enums\PlayerFaction;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GamePlayer>
 */
class GamePlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $turnOrder = $this->faker->numberBetween(0, 7);
        return [
            'game_id' => Game::factory(),
            'user_id' => User::factory(),
            'turn_order' => $turnOrder,
            'color' => PlayerColor::fromTurnOrder($turnOrder),
            'faction' => PlayerFaction::fromTurnOrder($turnOrder),
            'is_ready' => false,
            'joined_at' => now(),
            'gold' => config('game.starting_resources.gold', 100),
            'food' => config('game.starting_resources.food', 100),
            'wood' => config('game.starting_resources.wood', 50),
            'stone' => config('game.starting_resources.stone', 50),
            'iron' => config('game.starting_resources.iron', 0),
        ];
    }
}
