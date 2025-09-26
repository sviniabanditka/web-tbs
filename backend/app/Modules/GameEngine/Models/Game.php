<?php

namespace App\Modules\GameEngine\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = [
        'campaign_id',
        'status',
        'current_turn',
        'current_player_id',
        'game_data',
        'map_data',
    ];

    protected $casts = [
        'game_data' => 'array',
        'map_data' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Campaign\Models\Campaign::class);
    }

    public function currentPlayer(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Common\Models\User::class, 'current_player_id');
    }

    public function turns(): HasMany
    {
        return $this->hasMany(GameTurn::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(GameAction::class);
    }
}
