<?php

namespace Database\Factories;

use App\Enums\GameStatus;
use App\Enums\MapGenerationAlgorithm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'status' => GameStatus::WAITING,
            'current_turn' => 0,
            'max_players' => $this->faker->numberBetween(2, 8),
            'turn_time_limit' => 300,
            'action_points_per_turn' => 5,
            'map_generation_seed' => $this->faker->randomNumber(),
            'map_generation_algorithm' => MapGenerationAlgorithm::HYBRID,
            'map_size' => 20,
        ];
    }
}
