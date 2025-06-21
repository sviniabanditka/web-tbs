<?php

namespace App\Models;

use App\Enums\GameActionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'player_id',
        'turn_number',
        'action_type', // 'move', 'attack', 'build', 'upgrade', 'fortify', 'end_turn'
        'action_data', // JSON with action-specific data
        'action_points_cost',
        'source_hex_id',
        'target_hex_id',
        'unit_id',
        'building_id',
        'successful',
        'error_message',
        'executed_at',
    ];

    protected $casts = [
        'action_type' => GameActionType::class,
        'action_data' => 'array',
        'action_points_cost' => 'integer',
        'successful' => 'boolean',
        'executed_at' => 'datetime',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(GamePlayer::class);
    }

    public function sourceHex(): BelongsTo
    {
        return $this->belongsTo(GameHex::class, 'source_hex_id');
    }

    public function targetHex(): BelongsTo
    {
        return $this->belongsTo(GameHex::class, 'target_hex_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function getActionDescriptionAttribute(): string
    {
        $descriptions = [
            'move' => 'Moved unit',
            'attack' => 'Attacked',
            'build' => 'Built',
            'upgrade' => 'Upgraded',
            'fortify' => 'Fortified unit',
            'end_turn' => 'Ended turn',
            'recruit' => 'Recruited unit',
            'research' => 'Researched technology',
            'trade' => 'Traded resources',
        ];

        $baseDescription = $descriptions[$this->action_type] ?? 'Performed action';

        if ($this->unit) {
            $baseDescription .= " {$this->unit->type}";
        }

        if ($this->building) {
            $baseDescription .= " {$this->building->type}";
        }

        if ($this->targetHex) {
            $baseDescription .= " to ({$this->targetHex->q}, {$this->targetHex->r})";
        }

        return $baseDescription;
    }

    public function getActionPointsRemainingAttribute(): int
    {
        $usedPoints = $this->game->actions()
            ->where('player_id', $this->player_id)
            ->where('turn_number', $this->turn_number)
            ->where('id', '<=', $this->id)
            ->sum('action_points_cost');

        return $this->game->action_points_per_turn - $usedPoints;
    }

    public function canUndo(): bool
    {
        // Can only undo the last action of the current turn
        $lastAction = $this->game->actions()
            ->where('player_id', $this->player_id)
            ->where('turn_number', $this->turn_number)
            ->orderBy('id', 'desc')
            ->first();

        return $this->id === $lastAction->id && $this->successful;
    }

    public function getUndoCost(): int
    {
        // Undoing costs the same as the original action
        return $this->action_points_cost;
    }
}
