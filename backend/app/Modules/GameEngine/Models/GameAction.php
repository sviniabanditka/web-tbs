<?php

namespace App\Modules\GameEngine\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameAction extends Model
{
    protected $fillable = [
        'game_id',
        'turn_id',
        'player_id',
        'action_type',
        'action_data',
        'status',
        'executed_at',
    ];

    protected $casts = [
        'action_data' => 'array',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function turn(): BelongsTo
    {
        return $this->belongsTo(GameTurn::class, 'turn_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Common\Models\User::class, 'player_id');
    }
}
