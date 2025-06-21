<?php

namespace App\Services;

use App\Enums\GameStatus;
use App\Enums\PlayerColor;
use App\Enums\PlayerFaction;
use App\Events\GameStateChanged;
use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GameService
{
    public function __construct(
        private MapGenerationService $mapGenerationService
    ) {}

    public function createGame(array $validatedData, User $user): Game
    {
        return DB::transaction(function () use ($validatedData, $user) {
            $game = Game::create([
                'name' => $validatedData['name'],
                'status' => GameStatus::WAITING,
                'current_turn' => 0,
                'max_players' => $validatedData['max_players'],
                'turn_time_limit' => $validatedData['turn_time_limit'] ?? 300,
                'action_points_per_turn' => $validatedData['action_points_per_turn'],
                'map_size' => $validatedData['map_size'],
                'map_generation_seed' => mt_rand(1, 999999),
                'map_generation_algorithm' => $validatedData['map_generation_algorithm'],
                'terrain_parameters' => $validatedData['terrain_parameters'] ?? [],
                'created_by' => $user->id,
            ]);

            $startingResources = config('game.starting_resources', []);

            GamePlayer::create(array_merge(
                [
                    'game_id' => $game->id,
                    'user_id' => $user->id,
                    'turn_order' => 0,
                    'color' => PlayerColor::fromTurnOrder(0)->value,
                    'faction' => PlayerFaction::fromTurnOrder(0)->value,
                    'is_ready' => true,
                    'joined_at' => now(),
                ],
                $startingResources
            ));

            return $game;
        });
    }

    public function joinGame(Game $game, User $user): GamePlayer
    {
        if ($game->status !== GameStatus::WAITING) {
            throw new \Exception('Game is not accepting players');
        }

        if ($game->players()->where('user_id', $user->id)->exists()) {
            throw new \Exception('Already joined this game');
        }

        if ($game->players()->count() >= $game->max_players) {
            throw new \Exception('Game is full');
        }

        return DB::transaction(function () use ($game, $user) {
            $turnOrder = $game->players()->count();
            $startingResources = config('game.starting_resources', []);

            $player = GamePlayer::create(array_merge(
                [
                    'game_id' => $game->id,
                    'user_id' => $user->id,
                    'turn_order' => $turnOrder,
                    'color' => PlayerColor::fromTurnOrder($turnOrder)->value,
                    'faction' => PlayerFaction::fromTurnOrder($turnOrder)->value,
                    'is_ready' => false,
                    'joined_at' => now(),
                ],
                $startingResources
            ));

            GameStateChanged::dispatch($game, 'player_joined', ['player' => $player->load('user')]);

            return $player;
        });
    }

    public function leaveGame(Game $game, User $user): void
    {
        $player = $game->players()->where('user_id', $user->id)->first();

        if (!$player) {
            throw new \Exception('Not a player in this game');
        }

        if ($game->status === GameStatus::ACTIVE && $game->isPlayerTurn($user)) {
            throw new \Exception('Cannot leave during your turn');
        }

        DB::transaction(function () use ($game, $player) {
            $player->update(['left_at' => now()]);

            if ($game->status === GameStatus::WAITING && $game->players()->whereNull('left_at')->count() === 0) {
                $game->delete();
            } else {
                GameStateChanged::dispatch($game, 'player_left', ['player' => $player->load('user')]);
            }
        });
    }

    public function startGame(Game $game, User $user): void
    {
        if ($game->created_by !== $user->id) {
            throw new \Exception('Only the game creator can start the game');
        }

        if ($game->status !== GameStatus::WAITING) {
            throw new \Exception('Game is not in waiting status');
        }

        $activePlayers = $game->players()->whereNull('left_at')->count();
        if ($activePlayers < 2) {
            throw new \Exception('Need at least 2 players to start');
        }

        DB::transaction(function () use ($game) {
            $this->mapGenerationService->generateMap($game);

            $game->update([
                'status' => GameStatus::ACTIVE,
                'current_turn' => 1,
                'started_at' => now(),
            ]);

            $firstPlayer = $game->players()->whereNull('left_at')->orderBy('turn_order')->first();
            $game->turns()->create([
                'turn_number' => 1,
                'player_id' => $firstPlayer->id,
                'status' => 'active',
                'started_at' => now(),
                'time_remaining' => $game->turn_time_limit,
            ]);

            GameStateChanged::dispatch($game, 'game_started');
        });
    }
}
