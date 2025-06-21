<?php

namespace App\Enums;

enum GameStateChangeType: string
{
    case PLAYER_JOINED = 'player_joined';
    case PLAYER_LEFT = 'player_left';
    case GAME_STARTED = 'game_started';
    case GAME_FINISHED = 'game_finished';
    case UNIT_MOVED = 'unit_moved';
    case UNIT_ATTACKED = 'unit_attacked';
    case BUILDING_CONSTRUCTED = 'building_constructed';
    case BUILDING_UPGRADED = 'building_upgraded';
    case UNIT_RECRUITED = 'unit_recruited';
    case RESOURCES_UPDATED = 'resources_updated';
}
