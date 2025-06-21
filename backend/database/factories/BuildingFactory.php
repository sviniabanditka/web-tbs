<?php

namespace Database\Factories;

use App\Enums\BuildingType;
use App\Models\Game;
use App\Models\GameHex;
use App\Models\GamePlayer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Building>
 */
class BuildingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(BuildingType::cases());
        $config = config('game.buildings.' . $type->value, []);

        return [
            'game_id' => Game::factory(),
            'player_id' => GamePlayer::factory(),
            'hex_id' => GameHex::factory(),
            'type' => $type,
            'name' => $config['name'] ?? 'Building',
            'level' => 1,
            'health' => 100,
            'max_health' => 100,
            'production_rate' => 0,
            'storage_capacity' => 0,
            'defense_bonus' => 0,
            'is_capital' => false,
            'costs' => $config['costs'] ?? [],
            'constructed_at' => now(),
        ];
    }
}
