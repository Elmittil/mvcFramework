<?php

declare(strict_types=1);

namespace App\Dice;

/**
 * Class Dice.
 */
class Dice
{
    private ?int $roll = null;

    private int $faces = 6;

    public function __construct(int $facesVar = 6)
    {
            $this->faces = $facesVar;
    }

    public function roll(): int
    {
        $this->roll = rand(1, $this->faces);

        return $this->roll;
    }

    public function getLastRoll(): int
    {
        return $this->roll;
    }

    public function getFaces(): int
    {
        return $this->faces;
    }
}
