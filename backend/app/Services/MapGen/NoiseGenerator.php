<?php

namespace App\Services\MapGen;
/**
 * Perlin noise generator for creating natural biome distribution
 */
class NoiseGenerator
{
    private int $seed;
    private array $permutation;

    public function __construct(string $seedString)
    {
        $this->applySeed($seedString);
    }

    /**
     * Apply string seed
     */
    private function applySeed(string $seedString): void
    {
        $this->seed = crc32($seedString);
        mt_srand($this->seed);

        // Create permutation table for Perlin noise
        $this->permutation = range(0, 255);

        // Deterministic shuffling
        for ($i = 255; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$this->permutation[$i], $this->permutation[$j]] =
                [$this->permutation[$j], $this->permutation[$i]];
        }

        // Duplicate to avoid overflow
        $this->permutation = array_merge($this->permutation, $this->permutation);
    }

    /**
     * Generate Perlin noise value for coordinates
     */
    public function generatePerlinNoise(int $q, int $r): float
    {
        $x = $q + $r * 0.5;
        $y = $r * 0.866025404;

        // Change scale - was 0.1, make it larger for less clustered mountains
        $x *= 0.05; // was 0.1
        $y *= 0.05; // was 0.1

        return $this->perlin($x, $y);
    }

    /**
     * Main Perlin noise function
     */
    private function perlin(float $x, float $y): float
    {
        $xi = (int)floor($x) & 255;
        $yi = (int)floor($y) & 255;

        $xf = $x - floor($x);
        $yf = $y - floor($y);

        $u = $this->fade($xf);
        $v = $this->fade($yf);

        $aa = $this->permutation[$this->permutation[$xi] + $yi];
        $ab = $this->permutation[$this->permutation[$xi] + $yi + 1];
        $ba = $this->permutation[$this->permutation[$xi + 1] + $yi];
        $bb = $this->permutation[$this->permutation[$xi + 1] + $yi + 1];

        $x1 = $this->lerp($this->grad($aa, $xf, $yf),
                         $this->grad($ba, $xf - 1, $yf), $u);
        $x2 = $this->lerp($this->grad($ab, $xf, $yf - 1),
                         $this->grad($bb, $xf - 1, $yf - 1), $u);

        return $this->lerp($x1, $x2, $v);
    }

    private function fade(float $t): float
    {
        return $t * $t * $t * ($t * ($t * 6 - 15) + 10);
    }

    private function lerp(float $a, float $b, float $t): float
    {
        return $a + $t * ($b - $a);
    }

    private function grad(int $hash, float $x, float $y): float
    {
        $h = $hash & 15;
        $u = $h < 8 ? $x : $y;
        $v = ($h < 4 ? $y : $h) == 12 || $h == 14 ? $x : 0;
        return (($h & 1) == 0 ? $u : -$u) + (($h & 2) == 0 ? $v : -$v);
    }

    public function generateLayeredNoise(int $q, int $r): float
    {
        // Base layer
        $baseNoise = $this->generatePerlinNoise($q, $r);

        // Second layer with different scale
        $x = ($q + $r * 0.5) * 0.05; // Smaller scale
        $y = $r * 0.866025404 * 0.05;
        $detailNoise = $this->perlin($x, $y) * 0.5;

        // Third layer for fine details
        $x2 = ($q + $r * 0.5) * 0.2; // Larger scale
        $y2 = $r * 0.866025404 * 0.2;
        $microNoise = $this->perlin($x2, $y2) * 0.25;

        return $baseNoise + $detailNoise + $microNoise;
    }
}
