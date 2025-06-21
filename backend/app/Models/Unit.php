<?php

namespace App\Models;

use App\Contracts\AttackableInterface;
use App\Contracts\FortifiableInterface;
use App\Contracts\MovableInterface;
use App\Enums\UnitType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unit extends Model implements MovableInterface, AttackableInterface, FortifiableInterface
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'player_id',
        'hex_id',
        'type', // 'warrior', 'archer', 'cavalry', 'settler', 'worker', etc.
        'name',
        'level',
        'health',
        'max_health',
        'attack',
        'defense',
        'movement_range',
        'movement_points',
        'max_movement_points',
        'experience',
        'is_fortified',
        'fortified_turns',
        'created_at',
        'destroyed_at',
    ];

    protected $casts = [
        'type' => UnitType::class,
        'level' => 'integer',
        'health' => 'integer',
        'max_health' => 'integer',
        'attack' => 'integer',
        'defense' => 'integer',
        'movement_range' => 'integer',
        'movement_points' => 'integer',
        'max_movement_points' => 'integer',
        'experience' => 'integer',
        'is_fortified' => 'boolean',
        'fortified_turns' => 'integer',
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

    public function canMove(): bool
    {
        return !$this->isDestroyed() && $this->movement_points > 0;
    }

    public function canAttack(): bool
    {
        return !$this->isDestroyed() && !$this->is_fortified;
    }

    public function canFortify(): bool
    {
        return !$this->isDestroyed() && !$this->is_fortified && $this->movement_points === 0;
    }

    public function getCombatStrength(): array
    {
        $baseAttack = $this->attack;
        $baseDefense = $this->defense;

        // Apply terrain bonuses
        if ($this->hex) {
            $baseDefense += $this->hex->defense_bonus;
        }

        // Apply fortification bonus
        if ($this->is_fortified) {
            $baseDefense += 2;
        }

        // Apply experience bonus
        $experienceBonus = min($this->experience / 10, 5); // Max +5 from experience
        $baseAttack += $experienceBonus;
        $baseDefense += $experienceBonus;

        return [
            'attack' => $baseAttack,
            'defense' => $baseDefense,
        ];
    }

    public function getMovementCost(GameHex $targetHex): int
    {
        $baseCost = $targetHex->movement_cost;

        // Apply unit type modifiers
        switch ($this->type) {
            case 'cavalry':
                $baseCost = max(1, $baseCost - 1); // Cavalry moves faster
                break;
            case 'settler':
                $baseCost = max(1, $baseCost - 1); // Settlers move faster
                break;
            case 'worker':
                $baseCost = max(1, $baseCost - 1); // Workers move faster
                break;
        }

        return $baseCost;
    }

    public function canReachHex(GameHex $targetHex): bool
    {
        if (!$this->canMove()) {
            return false;
        }

        $distance = $this->hex->distanceTo($targetHex);
        $movementCost = $this->getMovementCost($targetHex);

        return $distance * $movementCost <= $this->movement_points;
    }

    public function getReachableHexes(): array
    {
        if (!$this->canMove()) {
            return [];
        }

        $reachable = [];
        $gameHexes = $this->game->hexes()->get();

        foreach ($gameHexes as $hex) {
            if ($this->canReachHex($hex)) {
                $reachable[] = $hex;
            }
        }

        return $reachable;
    }

    public function resetMovementPoints(): void
    {
        $this->update([
            'movement_points' => $this->max_movement_points,
            'is_fortified' => false,
            'fortified_turns' => 0,
        ]);
    }

    public function gainExperience(int $amount = 1): void
    {
        $this->increment('experience', $amount);

        // Check for level up
        $newLevel = min(5, ($this->experience / 10) + 1);
        if ($newLevel > $this->level) {
            $this->update([
                'level' => $newLevel,
                'attack' => $this->attack + 1,
                'defense' => $this->defense + 1,
                'max_health' => $this->max_health + 5,
                'health' => $this->max_health + 5, // Heal on level up
            ]);
        }
    }
}
