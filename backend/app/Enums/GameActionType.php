<?php

namespace App\Enums;

enum GameActionType: string
{
    case MOVE = 'move';
    case ATTACK = 'attack';
    case BUILD = 'build';
    case UPGRADE = 'upgrade';
    case FORTIFY = 'fortify';
    case END_TURN = 'end_turn';
    case RECRUIT = 'recruit';
    case RESEARCH = 'research';
    case TRADE = 'trade';
}
