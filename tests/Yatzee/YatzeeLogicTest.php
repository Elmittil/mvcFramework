<?php

declare(strict_types=1);

namespace App\Yatzee;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for the configuration file bootstrap.php.
 */
class YatzeeLogicClassTest extends TestCase
{

    /**
     * Construct object and verify that it is of the expected class (YatzeeLogic).
     */
    public function testCreateObject()
    {
        $scoreChart = new ScoreChart();
        $chartArray = $scoreChart->getScoreChart();

        $logic = new YatzeeLogic($chartArray);
        $this->assertInstanceOf("\App\Yatzee\YatzeeLogic", $logic);
    }

    /**
     * Construct object and verify that method startGame creates a new array
     * $rolledDiceValues.
     */
    public function testStartGame()
    {
        $scoreChart = new ScoreChart();
        $chartArray = $scoreChart->getScoreChart();
        $logic = new YatzeeLogic($chartArray);

        $logic->startGame();
        $res = $logic->getRolledDiceValues();
        $this->assertNotNull($res);
    }

    /**
     * Construct object and verify that method reRoll() generates
     * an array with a given ($diceQty) number of keys
     */
    public function testLogicReRoll()
    {
        $diceQty = 2;
        $scoreChart = new ScoreChart();
        $chartArray = $scoreChart->getScoreChart();
        $logic = new YatzeeLogic($chartArray);

        $res = $logic->reRoll($diceQty);
        $this->assertIsArray($res);
        $arrayLength = count($res);
        $this->assertEquals($arrayLength, $diceQty);
    }

    /**
     * Construct object and verify that method getScores returns an array
     */
    public function testGetScores()
    {
        $scoreChart = new ScoreChart();
        $chartArray = $scoreChart->getScoreChart();
        $logic = new YatzeeLogic($chartArray);

        $res = $logic->getScores();
        $this->assertIsArray($res);
    }

    /**
     * Construct object and verify that method scorableCombos returns an array
     * of the given length.
     */
    public function testScorableCombos()
    {
        $arrayLength = 6;
        $scoreChart = new ScoreChart();
        $chartArray = $scoreChart->getScoreChart();
        $logic = new YatzeeLogic($chartArray);

        $logic->startGame();
        $rolledDiceValues = $logic->getRolledDiceValues();

        $res = $logic->scorableCombos($rolledDiceValues);
        $this->assertNotNull($res);
        $this->assertCount($arrayLength, $res);
    }


    /**
     * Construct object and verify that method comboTotal returns an array
     * of the given length.
     */
    public function testComboTotal()
    {
        $arrayLength = 6;
        $scoreChart = new ScoreChart();
        $chartArray = $scoreChart->getScoreChart();
        $logic = new YatzeeLogic($chartArray);

        $logic->startGame();

        $rolledDiceValues = $logic->getRolledDiceValues();
        $combos = $logic->scorableCombos($rolledDiceValues);

        $res = $logic->comboTotal($combos);

        $this->assertNotNull($res);
        $this->assertCount($arrayLength, $res);
    }

    /**
     * Construct object and verify that logic method setScore changes values
     *  in ScoreChart to given values ($value, $face)
     */
    public function testSetScore()
    {
        $face = "2";
        $value = 6;

        $scoreChart = new ScoreChart();
        $chartArray = $scoreChart->getScoreChart();
        $logic = new YatzeeLogic($chartArray);

        $logic->setScore($face, $value);
        $res =  $logic->getScores()[$face];
        $this->assertEquals($value, $res);
    }
}
