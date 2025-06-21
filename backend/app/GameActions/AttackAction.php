<?php

namespace App\GameActions;

use App\Contracts\GameActionInterface;
use App\Enums\GameStateChangeType;
use App\Events\GameStateChanged;
use App\Http\Requests\ExecuteGameActionRequest;
use App\Models\Game;
use App\Models\GameHex;
use App\Models\GamePlayer;
use App\Models\Unit;

class AttackAction implements GameActionInterface
{
    public function getCost(): int
    {
        return 2;
    }

    public function execute(Game $game, GamePlayer $player, ExecuteGameActionRequest $request): array
    {
        $validated = $request->validated();
        $attacker = Unit::findOrFail($validated['unit_id']);
        $targetHex = GameHex::where('game_id', $game->id)
            ->where('q', $validated['target_hex_q'])
            ->where('r', $validated['target_hex_r'])
            ->firstOrFail();
        $defender = $targetHex->unit;

        if ($attacker->player_id !== $player->id || !$defender || $defender->player_id === $player->id || !$attacker->canAttack() || !$attacker->hex->isAdjacentTo($targetHex)) {
            throw new \Exception('Invalid attack');
        }

        $attackerStrength = $attacker->getCombatStrength();
        $defenderStrength = $defender->getCombatStrength();
        $damage = max(0, (mt_rand(1, 20) + $attackerStrength['attack']) - (mt_rand(1, 20) + $defenderStrength['defense']));
        $defenderDestroyed = false;

        if ($damage > 0) {
            $defender->update(['health' => max(0, $defender->health - $damage)]);
            if ($defender->health <= 0) {
                $defender->update(['destroyed_at' => now()]);
                $defenderDestroyed = true;
            }
            $attacker->gainExperience();
        }

        $attacker->update(['movement_points' => 0]);

        GameStateChanged::dispatch($game, GameStateChangeType::UNIT_ATTACKED, compact('attacker', 'defender', 'damage', 'defenderDestroyed'));

        return compact('damage', 'defenderDestroyed');
    }
}