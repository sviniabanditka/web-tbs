<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameTurn extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'turn_number',
        'player_id',
        'status', // 'pending', 'active', 'completed', 'skipped'
        'started_at',
        'ended_at',
        'time_remaining',
        'actions_performed',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'time_remaining' => 'integer',
        'actions_performed' => 'integer',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(GamePlayer::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(GameAction::class, 'turn_number', 'turn_number')
            ->where('game_id', $this->game_id);
    }

    public function getActionPointsUsedAttribute(): int
    {
        return $this->actions()->sum('action_points_cost');
    }

    public function getActionPointsRemainingAttribute(): int
    {
        return $this->game->action_points_per_turn - $this->action_points_used;
    }
}
