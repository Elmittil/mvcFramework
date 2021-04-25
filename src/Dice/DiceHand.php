<?php

declare(strict_types=1);

namespace App\Dice;

/**
 * Class DiceHand.
 */
class DiceHand
{
    private array $allDice;

    const FACES = 6;
    private string $type;
    private int $sum = 0;

    public function __construct(int $diceQty = 1, string $type = "regular")
    {
        if ($type == "regular") {
            for ($i = 0; $i < $diceQty; $i++) {
                $this->allDice[$i] = new Dice(self::FACES);
            }
            $this->type = $type;
        }

        if ($type == "graphic") {
            for ($i = 0; $i < $diceQty; $i++) {
                $this->allDice[$i] = new GraphicDice();
            }
            $this->type = $type;
        }
    }

    public function roll(int $diceQty): void
    {
        $this->sum = 0;
        for ($i = 0; $i < $diceQty; $i++) {
            $this->sum += $this->allDice[$i]->roll();
        }
    }

    public function getLastHandRoll(int $diceQty): string
    {
        $res = "";
        for ($i = 0; $i < $diceQty; $i++) {
            $res .= $this->allDice[$i]->getLastRoll() . ", ";
        }
        return $res . " = " . $this->sum;
    }

    public function getLastHandRollArray(int $diceQty): array
    {
        $res = array();
        for ($i = 0; $i < $diceQty; $i++) {
            $res[$i] = $this->allDice[$i]->getLastRoll();
        }
        return $res;
    }

    public function getRollSum(): int
    {
        return $this->sum;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAllDice(): array
    {
        return $this->allDice;
    }
}
