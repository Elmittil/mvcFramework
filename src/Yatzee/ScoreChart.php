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

    public function recordScore(string $face, int $value): array
    {
        if ($this->chart[$face] == null) {
            $this->chart[$face] = $value;
        }

        $this->chart["Total"] = $this->chart["Total"] + $value;

        $plays = $this->chart["playsLeft"];
        $plays = $plays - 1;

        if ($plays == 0) {
            if ($this->chart["Total"] >= 63) {
                $this->chart["Bonus"] = $this->bonus;
                $this->chart["Total"] = $this->chart["Total"] + $this->bonus;
            }
        }

        $this->chart["playsLeft"] = $plays;
        return $this->chart;
    }

    public function getScoreChart()
    {
        return $this->chart;
    }
}
