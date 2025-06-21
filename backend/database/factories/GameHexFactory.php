<?php

namespace Database\Factories;

use App\Enums\TerrainType;
use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameHex>
 */
class GameHexFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'game_id' => Game::factory(),
            'q' => $this->faker->numberBetween(-10, 10),
            'r' => $this->faker->numberBetween(-10, 10),
            'terrain_type' => $this->faker->randomElement(TerrainType::cases()),
            'resource_type' => null,
            'elevation' => $this->faker->randomFloat(2, 0, 1),
            'moisture' => $this->faker->randomFloat(2, 0, 1),
            'temperature' => $this->faker->randomFloat(2, 0, 1),
            'resource_amount' => null,
            'movement_cost' => 1,
            'defense_bonus' => 0,
            'visibility_bonus' => 0,
            'production_bonus' => json_encode([]),
        ];
    }
}
