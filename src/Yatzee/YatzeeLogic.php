<?php

declare(strict_types=1);

namespace Elmittil\Yatzee;

use Elmittil\Dice\DiceHand;

/**
 * Class YatzeeLogic.
 */
class YatzeeLogic
{
    private DiceHand $diceHand;
    private array $rolledDiceValues;
    private int $diceQty = 5;
    private ScoreChart $scoreChart;

    public function __construct(array $chart = null)
    {
        //cretae new scoring sheet
        $this->scoreChart = new ScoreChart($chart);
        //create dice hand with graphic dice
        $this->diceHand = new DiceHand($this->diceQty, "graphic");
    }


    public function startGame(): void
    {
        $this->rollHand();
    }

    public function rollHand(): void
    {
        $this->diceHand->roll($this->diceQty);
        $this->rolledDiceValues = $this->diceHand->getLastHandRollArray($this->diceQty);
    }

    public function reRoll(int $newDiceqty): array
    {
        $newDiceValues = array();
        $rerollDiceHand = new DiceHand($newDiceqty, "graphic");
        $rerollDiceHand->roll($newDiceqty);
        $newDiceValues = $rerollDiceHand->getLastHandRollArray($newDiceqty);
        return $newDiceValues;
    }

    public function getRolledDiceValues(): array
    {
        return $this->rolledDiceValues;
    }

    public function getScores(): array
    {
        return $this->scoreChart->getScoreChart();
    }

    public function scorableCombos(array $rolledDiceValues): array
    {
        $values = $rolledDiceValues;

        $scorecombos = array(
            "1" => 0,
            "2" => 0,
            "3" => 0,
            "4" => 0,
            "5" => 0,
            "6" => 0,
        );

        for ($i = 1; $i <= 6; $i++) {
            foreach ($values as $possibleDuplicate) {
                if ($possibleDuplicate == $i) {
                    $scorecombos["$i"] ++;
                }
            }
        }
        return $scorecombos;
    }

    public function comboTotal(array $combos): array
    {
        $totalPossibleScores = array(
            "1" => 0,
            "2" => 0,
            "3" => 0,
            "4" => 0,
            "5" => 0,
            "6" => 0,
        );

        foreach ($combos as $key => $value) {
            $totalPossibleScores[$key] = intval($key) * $value;
        }

        return $totalPossibleScores;
    }

    public function setScore(string $face, int $value)
    {
        $this->scoreChart->recordScore($face, $value);
    }
}
