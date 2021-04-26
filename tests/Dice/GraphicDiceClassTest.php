<?php

declare(strict_types=1);

namespace App\Dice;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for the configuration file bootstrap.php.
 */
class GarphicDiceClassTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testGraphic()
    {
        $dice = new GraphicDice();
        $this->assertInstanceOf("\App\Dice\GraphicDice", $dice);

        $string = $dice->roll();
        $string = $dice->graphic();
        $this->assertStringContainsString("dice-", $string);
    }
}
