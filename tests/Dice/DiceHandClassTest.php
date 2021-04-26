<?php

declare(strict_types=1);

namespace App\Dice;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for the configuration file bootstrap.php.
 */
class DiceHandClassTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateObjectNoArguments()
    {
        $diceHand = new DiceHand();
        $this->assertInstanceOf("\App\Dice\DiceHand", $diceHand);

        $dice = count($diceHand->getAllDice());
        $exp = 1;
        $this->assertEquals($exp, $dice);

        $type = $diceHand->getType();
        $exp = "regular";
        $this->assertEquals($exp, $type);
    }

    /**
     * Construct object and verify that the object has the expected
     * properties, use expected argum,ents ("graphic" and a pos int).
     */
    public function testCreateObjectValidArguments()
    {
        $diceQty = 5;
        $diceHand = new DiceHand($diceQty, "graphic");
        $this->assertInstanceOf("\App\Dice\DiceHand", $diceHand);

        $dice = count($diceHand->getAllDice());
        $exp = $diceQty;
        $this->assertEquals($exp, $dice);

        $type = $diceHand->getType();
        $exp = "graphic";
        $this->assertEquals($exp, $type);
    }

    /**
     * Construct object and verify that roll() changes the sum to
     * greater than 0
     */
    public function testRoll()
    {
        $diceQty = 2;
        $diceHand = new DiceHand($diceQty);
        $this->assertInstanceOf("\App\Dice\DiceHand", $diceHand);

        $diceHand->roll($diceQty);
        $sum = $diceHand->getRollSum();
        $exp = $sum > 0;
        $this->assertTrue($exp);
    }

    /**
     * Construct object and verify that getLastHandRoll() returns a string
     */
    public function testGetLastHandRoll()
    {
        $diceQty = 2;
        $diceHand = new DiceHand($diceQty);
        $this->assertInstanceOf("\App\Dice\DiceHand", $diceHand);

        $diceHand->roll($diceQty);
        $sum = $diceHand->getLastHandRoll($diceQty);
        $this->assertIsString($sum);
    }

     /**
     * Construct object and verify that getLastHandRollArray() returns an Array
     */
    public function testGetLastHandRollArray()
    {
        $diceQty = 2;
        $diceHand = new DiceHand($diceQty);

        $diceHand->roll($diceQty);
        $array = $diceHand->getLastHandRollArray($diceQty);
        $this->assertIsArray($array);
    }
}
