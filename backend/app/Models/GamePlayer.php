<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\PlayerColor;

class GamePlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'user_id',
        'turn_order',
        'color',
        'faction',
        'is_ready',
        'joined_at',
        'left_at',
        'gold',
        'food',
        'wood',
        'stone',
        'iron',
    ];

    protected $casts = [
        'is_ready' => 'boolean',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(GameAction::class);
    }

    public function getRemainingActionPointsAttribute(): int
    {
        return $this->game->getRemainingActionPoints($this);
    }

    public function hasResources(array $costs): bool
    {
        foreach ($costs as $resource => $amount) {
            if ($this->{$resource} < $amount) {
                return false;
            }
        }
        return true;
    }

    public function spendResources(array $costs): void
    {
        if (!$this->hasResources($costs)) {
            throw new \Exception('Not enough resources.');
        }

        foreach ($costs as $resource => $amount) {
            $this->{$resource} -= $amount;
        }
        $this->save();
    }
}
