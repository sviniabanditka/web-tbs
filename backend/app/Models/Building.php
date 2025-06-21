<?php

namespace App\Models;

use App\Contracts\RecruiterInterface;
use App\Contracts\UpgradableInterface;
use App\Enums\BuildingType;
use App\Enums\UnitType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Building extends Model implements UpgradableInterface, RecruiterInterface
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'player_id',
        'hex_id',
        'type', // 'city', 'farm', 'mine', 'barracks', 'temple', etc.
        'name',
        'level',
        'health',
        'max_health',
        'production_rate',
        'storage_capacity',
        'defense_bonus',
        'is_capital',
        'constructed_at',
        'destroyed_at',
    ];

    protected $casts = [
        'type' => BuildingType::class,
        'level' => 'integer',
        'health' => 'integer',
        'max_health' => 'integer',
        'production_rate' => 'integer',
        'storage_capacity' => 'integer',
        'defense_bonus' => 'integer',
        'is_capital' => 'boolean',
        'constructed_at' => 'datetime',
        'destroyed_at' => 'datetime',
        'costs' => 'array',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(GamePlayer::class);
    }

    public function hex(): BelongsTo
    {
        return $this->belongsTo(GameHex::class);
    }

    public function getHealthPercentageAttribute(): float
    {
        return $this->max_health > 0 ? ($this->health / $this->max_health) * 100 : 0;
    }

    public function isDestroyed(): bool
    {
        return $this->destroyed_at !== null || $this->health <= 0;
    }

    public function canUpgrade(): bool
    {
        // Add specific upgrade logic here, e.g., check resources, technology level
        return $this->level < 5; // Example: max level is 5
    }

    public function getUpgradeCost(): array
    {
        $baseCosts = [
            'city' => ['gold' => 100, 'iron' => 50, 'wood' => 30],
            'farm' => ['gold' => 50, 'wood' => 20],
            'mine' => ['gold' => 80, 'iron' => 20],
            'barracks' => ['gold' => 120, 'iron' => 80, 'wood' => 40],
            'temple' => ['gold' => 200, 'wood' => 60],
        ];

        $costs = $baseCosts[$this->type] ?? ['gold' => 100];

        // Increase cost with level
        foreach ($costs as $resource => $amount) {
            $costs[$resource] = $amount * ($this->level + 1);
        }

        return $costs;
    }

    public function canRecruit(UnitType $unitType): bool
    {
        if ($this->type !== BuildingType::BARRACKS) {
            return false;
        }

        if ($this->hex->unit) {
            return false;
        }

        // Add more logic here, e.g., check required resources, technology for the unit type
        return true;
    }
}
