<?php

declare(strict_types=1);

namespace App\Yatzee;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for the configuration file bootstrap.php.
 */
class ScoreChartClassTest extends TestCase
{
    /**
     * Construct object and verify that the method returns an array with changed values.
     */
    public function testRecordScore()
    {
        $face = 6;
        $score = 36;

        $chart = new ScoreChart();
        $this->assertInstanceOf("\App\Yatzee\ScoreChart", $chart);

        $res = $chart->recordScore((string)$face, $score);
        $exp = $score;
        $this->assertEquals($exp, $res[(string)$face]);
    }

    /**
     * Construct object and verify that the method returns an array with changed values.
     */
    public function testGetScoreChart()
    {
        $chart = new ScoreChart();
        $this->assertInstanceOf("\App\Yatzee\ScoreChart", $chart);

        $res = $chart->getScoreChart();
        $this->assertIsArray($res);
        $arrayValue = $res["5"];
        $this->assertNull($arrayValue);
    }

    /**
     * Construct object and verify that the method returns an array with changed values.
     */
    public function testRecordScorePlaysLeftLimit()
    {
        $score = 36;

        $chart = new ScoreChart();
        $this->assertInstanceOf("\App\Yatzee\ScoreChart", $chart);

        $res = array();
        for ($test = 1; $test < 7; $test++) {
            $res = $chart->recordScore((string)($test), $score);
        }

        $exp = 35;
        $this->assertEquals($exp, $res["Bonus"]);
    }
}
