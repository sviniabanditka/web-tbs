<?php

namespace App\Modules\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameStatistic extends Model
{
    protected $fillable = [
        'game_id',
        'player_id',
        'stat_type',
        'value',
        'recorded_at',
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
