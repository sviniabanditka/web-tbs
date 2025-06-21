<?php

namespace App\Enums;

enum PlayerFaction: string
{
    case EMPIRE = 'empire';
    case REPUBLIC = 'republic';
    case FEDERATION = 'federation';
    case ALLIANCE = 'alliance';
    case CONFEDERATION = 'confederation';
    case UNION = 'union';
    case LEAGUE = 'league';
    case COALITION = 'coalition';

    public static function fromTurnOrder(int $turnOrder): self
    {
        $cases = self::cases();
        return $cases[$turnOrder % count($cases)];
    }
}
