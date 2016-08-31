<?php

use PHPUnit\Framework\TestCase;

require __DIR__ . '/../src/Dice.php';

class DiceTest extends TestCase {

    public function testRoll() {
        $apiKey = 'b9a77b3a-4325-4e7e-856f-948e3f0bce5a';
        $dice = new Dice($apiKey);
        $averageNumberOfTurns = 472;
        for ($i=0; $i < $averageNumberOfTurns; $i++) { 
            $number = $dice->roll();
            $this->assertGreaterThan(0, $number);
            $this->assertLessThan(7, $number);
        }
    }
}

?>