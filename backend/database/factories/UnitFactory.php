<?php

namespace Database\Factories;

use App\Enums\UnitType;
use App\Models\Game;
use App\Models\GameHex;
use App\Models\GamePlayer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(UnitType::cases());
        $config = config('game.units.' . $type->value, []);
        $stats = $config['stats'] ?? [];

        return [
            'game_id' => Game::factory(),
            'player_id' => GamePlayer::factory(),
            'hex_id' => GameHex::factory(),
            'type' => $type,
            'name' => $config['name'] ?? 'Unit',
            'level' => 1,
            'health' => $stats['health'] ?? 100,
            'max_health' => $stats['health'] ?? 100,
            'attack' => $stats['attack'] ?? 5,
            'defense' => $stats['defense'] ?? 5,
            'movement_range' => $stats['movement_range'] ?? 2,
            'movement_points' => $stats['movement_range'] ?? 2,
            'max_movement_points' => $stats['movement_range'] ?? 2,
            'costs' => $config['costs'] ?? [],
        ];
    }
}
