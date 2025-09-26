<?php

namespace App\Modules\Combat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unit extends Model
{
    protected $fillable = [
        'game_id',
        'player_id',
        'unit_type',
        'position_q',
        'position_r',
        'health',
        'max_health',
        'attack',
        'defense',
        'movement',
        'status',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\GameEngine\Models\Game::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Common\Models\User::class, 'player_id');
    }
}
