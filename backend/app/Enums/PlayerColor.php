<?php

namespace App\Enums;

enum PlayerColor: string
{
    case RED = '#FF6B6B';
    case TEAL = '#4ECDC4';
    case BLUE = '#45B7D1';
    case GREEN = '#96CEB4';
    case YELLOW = '#FFEAA7';
    case PLUM = '#DDA0DD';
    case MINT = '#98D8C8';
    case GOLD = '#F7DC6F';

    public static function fromTurnOrder(int $turnOrder): self
    {
        $cases = self::cases();
        return $cases[$turnOrder % count($cases)];
    }
}
