<?php

namespace App\Services;

use App\Contracts\GameActionInterface;
use App\Enums\GameActionType;
use App\GameActions\AttackAction;
use App\GameActions\BuildAction;
use App\GameActions\EndTurnAction;
use App\GameActions\FortifyAction;
use App\GameActions\MoveAction;
use App\GameActions\RecruitAction;
use App\GameActions\UpgradeAction;
use Illuminate\Contracts\Container\Container;

class GameActionFactory
{
    protected array $actionMap = [
        GameActionType::MOVE->value => MoveAction::class,
        GameActionType::ATTACK->value => AttackAction::class,
        GameActionType::BUILD->value => BuildAction::class,
        GameActionType::UPGRADE->value => UpgradeAction::class,
        GameActionType::FORTIFY->value => FortifyAction::class,
        GameActionType::RECRUIT->value => RecruitAction::class,
        GameActionType::END_TURN->value => EndTurnAction::class,
    ];

    public function __construct(protected Container $container)
    {
    }

    public function make(GameActionType $actionType): GameActionInterface
    {
        $class = $this->actionMap[$actionType->value] ?? null;

        if (!$class || !class_exists($class)) {
            throw new \InvalidArgumentException("Action class for type {$actionType->value} not found.");
        }

        $instance = $this->container->make($class);

        if (!$instance instanceof GameActionInterface) {
            throw new \LogicException("Class {$class} must implement GameActionInterface.");
        }

        return $instance;
    }
}
