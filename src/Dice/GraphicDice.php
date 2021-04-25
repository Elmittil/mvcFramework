<?php

declare(strict_types=1);

namespace App\Dice;

/**
 * Class Dice.
 */
class GraphicDice extends Dice
{

    const SIDES = 6;

    public function __construct()
    {
        parent::__construct(self::SIDES);
    }

    public function graphic(): string
    {
        return "dice-" . $this->getLastRoll();
    }
}
