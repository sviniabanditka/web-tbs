<?php

namespace App\Modules\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'game_id',
        'user_id',
        'message',
        'type',
        'is_system',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\GameEngine\Models\Game::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Common\Models\User::class);
    }
}
