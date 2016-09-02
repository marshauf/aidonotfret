<?php
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../src/Game.php';
require __DIR__ . '/../src/AI.php';

class GameTest extends TestCase
{
    public function gameProvider() {
        return [
        [new Game([new AI("bob"), new AI("alice"), new AI("sarah"), new AI("rob")])],
        [new Game([new AI("bob"), new AI("alice"), new AI("sarah")])],
        [new Game([new AI("bob"), new AI("alice")])],
        ];
    }
    
    
    /**
    * @dataProvider gameProvider
    */
    public function testFullGame($game) {
        $count = 1;
        // Limit turns to 3000
        while($game->finished() == false && $count < 3000) {
            $turn = $game->playTurn();
            $this->assertEquals($count, $turn->number);
            $count++;
        }
        $this->assertEquals(true, $game->finished());
    }
}