<?php

namespace App\GameActions;

use App\Contracts\GameActionInterface;
use App\Http\Requests\ExecuteGameActionRequest;
use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Unit;

class FortifyAction implements GameActionInterface
{
    public function getCost(): int
    {
        return 1;
    }

    public function execute(Game $game, GamePlayer $player, ExecuteGameActionRequest $request): array
    {
        $validated = $request->validated();
        $unit = Unit::findOrFail($validated['unit_id']);

        if ($unit->player_id !== $player->id || !$unit->canFortify()) {
            throw new \Exception('Unit cannot fortify');
        }

        $unit->update(['is_fortified' => true, 'fortified_turns' => 1]);

        return ['is_fortified' => true];
    }
}
