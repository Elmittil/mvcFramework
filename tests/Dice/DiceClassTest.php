<?php

declare(strict_types=1);

namespace App\Dice;

use PHPUnit\Framework\TestCase;

// use App\Dice\Dice;

/**
 * Test cases for the configuration file bootstrap.php.
 */
class DiceClassTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateObjectNoArguments()
    {
        $dice = new Dice();
        $this->assertInstanceOf("\App\Dice\Dice", $dice);

        $res = $dice->getFaces();
        $exp = 6;
        $this->assertEquals($exp, $res);
    }

    /**
     * Construct object and verify that the object has the expected
     * properties, use a positive Int
     */
    public function testCreateObjectWithValidArguments()
    {
        $exp = 4;

        $dice = new Dice($exp);
        $this->assertInstanceOf("\App\Dice\Dice", $dice);

        $res = $dice->getFaces();
        $this->assertEquals($exp, $res);
    }

    // /**
    //  * Construct object and verify that the object has the expected
    //  * properties, use invalid argument.
    //  */
    // public function testCreateObjectWithInvalidArguments()
    // {
    //     $dice = new Dice("4");
    //     $this->assertInstanceOf("\App\Dice\Dice", $dice);

    //     $res = $dice->getFaces();
    //     $exp = 4;
    //     $this->assertEquals($exp, $res);
    // }

    /**
     * Construct object and verify that rollDice returns a value between
     * 1 and $faces.
     */
    public function testRollDice()
    {
        $faces = 4;
        $dice = new Dice($faces);
        $this->assertInstanceOf("\App\Dice\Dice", $dice);

        $roll = $dice->roll();

        $res = $roll > 0 && $roll <= $faces;
        $this->assertTrue($res);
    }

    /**
     * Construct object and verify that getLastRoll returns a value between
     * 1 and $faces.
     */
    public function testGetLastRoll()
    {
        $faces = 10;
        $dice = new Dice($faces);
        $this->assertInstanceOf("\App\Dice\Dice", $dice);

        $dice->roll();
        $roll = $dice->getLastRoll();

        $res = $roll > 0 && $roll <= $faces;
        $this->assertTrue($res);
    }
}
