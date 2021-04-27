<?php

declare(strict_types=1);

namespace App\Yatzee;

/**
 * Class ScoreChart.
 */
class ScoreChart
{
    private int $bonus = 35;
    private int $playsLeft = 6;
    private array $chart;

    public function __construct(array $currentChart = null)
    {
        if (is_null($currentChart)) {
            $this->chart = array(
                "1" => null,
                "2" => null,
                "3" => null,
                "4" => null,
                "5" => null,
                "6" => null,
                "Bonus" => 0,
                "Total" => 0,
                "playsLeft" => 6
            );
            return;
        }
        $this->chart = $currentChart;
    }

    // private function checkIfFull(): bool
    // {
    //     for ($i = 0; $i < 6; $i++)
    //     {
    //         if (empty($this->chart[$i]))
    //         {
    //             return false;
    //         }
    //     }
    //     return true;
    // }

    public function recordScore(string $face, int $value): array
    {
        if ($this->chart[$face] == null) {
            $this->chart[$face] = $value;
        }

        $this->chart["Total"] = $this->chart["Total"] + $value;

        $this->playsLeft --;
        $this->chart["playsLeft"] = $this->playsLeft;

        if ($this->playsLeft == 0) {
            if ($this->chart["Total"] >= 63) {
                $this->chart["Bonus"] = $this->bonus;
                $this->chart["Total"] = $this->chart["Total"] + $this->bonus;
            }
        }
        return $this->chart;
    }

    public function getScoreChart()
    {
        return $this->chart;
    }
}
