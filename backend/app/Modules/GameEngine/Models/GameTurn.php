<?php

namespace App\Modules\GameEngine\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameTurn extends Model
{
    protected $fillable = [
        'game_id',
        'player_id',
        'turn_number',
        'status',
        'started_at',
        'ended_at',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Common\Models\User::class, 'player_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(GameAction::class);
    }
}
