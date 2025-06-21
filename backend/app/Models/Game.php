<?php

namespace App\Models;

use App\Enums\GameStatus;
use App\Enums\MapGenerationAlgorithm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status', // 'waiting', 'active', 'paused', 'finished'
        'current_turn',
        'max_players',
        'turn_time_limit',
        'action_points_per_turn',
        'map_generation_seed',
        'map_generation_algorithm', // 'perlin', 'voronoi', 'hybrid'
        'map_size', // radius of the hexagonal map
        'terrain_parameters', // JSON with generation parameters
        'created_by',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'status' => GameStatus::class,
        'map_generation_algorithm' => MapGenerationAlgorithm::class,
        'terrain_parameters' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'turn_time_limit' => 'integer',
        'action_points_per_turn' => 'integer',
        'map_size' => 'integer',
    ];

    public function players(): HasMany
    {
        return $this->hasMany(GamePlayer::class);
    }

    public function turns(): HasMany
    {
        return $this->hasMany(GameTurn::class);
    }

    public function currentTurn(): BelongsTo
    {
        return $this->belongsTo(GameTurn::class, 'current_turn');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function hexes(): HasMany
    {
        return $this->hasMany(GameHex::class);
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(GameAction::class);
    }

    public function getActivePlayerAttribute()
    {
        return $this->players()->where('turn_order', $this->current_turn % $this->players()->count())->first();
    }

    public function isPlayerTurn(User $user): bool
    {
        $player = $this->players()->where('user_id', $user->id)->first();
        return $player && $player->turn_order === ($this->current_turn % $this->players()->count());
    }

    public function getRemainingActionPoints(GamePlayer $player): int
    {
        if (!$player) return 0;

        $usedPoints = $this->actions()
            ->where('player_id', $player->id)
            ->where('turn_number', $this->current_turn)
            ->sum('action_points_cost');

        return $this->action_points_per_turn - $usedPoints;
    }
}
