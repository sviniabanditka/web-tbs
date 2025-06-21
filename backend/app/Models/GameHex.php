<?php

namespace App\Models;

use App\Enums\TerrainType;
use App\Enums\ResourceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GameHex extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'q', // axial coordinate q
        'r', // axial coordinate r
        'terrain_type', // 'grass', 'forest', 'mountain', 'water', 'desert', etc.
        'elevation',
        'moisture',
        'temperature',
        'resource_type', // 'iron', 'gold', 'food', 'wood', etc.
        'resource_amount',
        'is_passable',
        'movement_cost',
        'defense_bonus',
        'production_bonus',
    ];

    protected $casts = [
        'q' => 'integer',
        'r' => 'integer',
        'terrain_type' => TerrainType::class,
        'resource_type' => ResourceType::class,
        'elevation' => 'float',
        'moisture' => 'float',
        'temperature' => 'float',
        'resource_amount' => 'integer',
        'is_passable' => 'boolean',
        'movement_cost' => 'integer',
        'defense_bonus' => 'integer',
        'production_bonus' => 'integer',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function building(): HasOne
    {
        return $this->hasOne(Building::class);
    }

    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class);
    }

    // Helper methods for hexagonal grid operations
    public function getSCoordinateAttribute(): int
    {
        // Calculate s coordinate from q + r + s = 0
        return -$this->q - $this->r;
    }

    public function getCubeCoordinatesAttribute(): array
    {
        return [
            'q' => $this->q,
            'r' => $this->r,
            's' => $this->s_coordinate,
        ];
    }

    public function getOffsetCoordinatesAttribute(): array
    {
        // Convert axial to offset coordinates (odd-r)
        $col = $this->q + ($this->r - ($this->r & 1)) / 2;
        $row = $this->r;
        return ['col' => $col, 'row' => $row];
    }

    public function getPixelCoordinatesAttribute(): array
    {
        // Convert to pixel coordinates for rendering
        $size = 1; // hex size
        $x = $size * (3/2 * $this->q);
        $y = $size * (sqrt(3)/2 * $this->q + sqrt(3) * $this->r);
        return ['x' => $x, 'y' => $y];
    }

    public function getNeighborsAttribute(): array
    {
        // Get all 6 neighboring hex coordinates
        $directions = [
            [1, 0], [1, -1], [0, -1],
            [-1, 0], [-1, 1], [0, 1]
        ];

        $neighbors = [];
        foreach ($directions as [$dq, $dr]) {
            $neighbors[] = [
                'q' => $this->q + $dq,
                'r' => $this->r + $dr,
            ];
        }

        return $neighbors;
    }

    public function distanceTo(GameHex $other): int
    {
        return $this->distanceToCoordinates($other->q, $other->r);
    }

    public function distanceToCoordinates(int $q, int $r): int
    {
        // Calculate distance using cube coordinates
        $s1 = $this->s_coordinate;
        $s2 = -$q - $r;

        return (abs($this->q - $q) + abs($this->r - $r) + abs($s1 - $s2)) / 2;
    }

    public function isAdjacentTo(GameHex $other): bool
    {
        return $this->distanceTo($other) === 1;
    }

    public function getLineTo(GameHex $other): array
    {
        // Get all hexes in a line from this hex to the other hex
        $distance = $this->distanceTo($other);
        if ($distance === 0) return [$this];

        $hexes = [];
        for ($i = 0; $i <= $distance; $i++) {
            $t = $i / $distance;
            $q = round($this->q + ($other->q - $this->q) * $t);
            $r = round($this->r + ($other->r - $this->r) * $t);
            $hexes[] = ['q' => $q, 'r' => $r];
        }

        return $hexes;
    }

    public function getRing(int $radius): array
    {
        // Get all hexes in a ring around this hex at given radius
        if ($radius === 0) return [$this];

        $hexes = [];
        $current = $this->getNeighborInDirection(4, $radius); // Start from direction 4

        for ($direction = 0; $direction < 6; $direction++) {
            for ($step = 0; $step < $radius; $step++) {
                $hexes[] = $current;
                $current = $this->getNeighborInDirection($direction, 1, $current);
            }
        }

        return $hexes;
    }

    private function getNeighborInDirection(int $direction, int $distance, array $start = null): array
    {
        $directions = [
            [1, 0], [1, -1], [0, -1],
            [-1, 0], [-1, 1], [0, 1]
        ];

        $q = ($start['q'] ?? $this->q) + $directions[$direction][0] * $distance;
        $r = ($start['r'] ?? $this->r) + $directions[$direction][1] * $distance;

        return ['q' => $q, 'r' => $r];
    }
}
