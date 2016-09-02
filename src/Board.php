<?php

require __DIR__ . '/Field.php';

class Pawn {
    public $player;
    public $field;
    public $id;

    public function __construct($player, $field, $id) {
        $this->player = $player;
        $this->field = $field;
        $this->id = $id;
    }
}

class Move {
    public $target;
    public $pawn;
    public function __construct($pawn, $target) {
        $this->pawn = $pawn;
        $this->target = $target;
    }
}

class Board
{
    private static function makeHomeFields() {
        $fourth = new HomeField();
        $thrid = new HomeField($fourth);
        $second = new HomeField($thrid);
        $first = new HomeField($second);
        return $first;
    }
    
    private static function makeSection($player, $entrance) {
        $exit = new ExitField($entrance, self::makeHomeFields(), $player);
        $node = new Field($exit);
        for ($i=0; $i < 7; $i++) {
            $node = new Field($node);
        }
        return $node;
    }
    
    private $pawns;
    private $startFields;
    
    public function __construct() {
        // construct board in reverse order
        // player yellow
        $entranceYellow = new EntranceField(null);
        $startFieldsYellow = new StartFields($entranceYellow);
        $node = self::makeSection(0, $entranceYellow);
        
        // player green
        $entranceGreen = new EntranceField($node);
        $startFieldsGreen = new StartFields($entranceGreen);
        $node = self::makeSection(1, $entranceGreen);
        
        // player blue
        $entranceBlue = new EntranceField($node);
        $startFieldsBlue = new StartFields($entranceBlue);
        $node = self::makeSection(2, $entranceBlue);
        
        // player red
        $entranceRed = new EntranceField($node);
        $startFieldsRed = new StartFields($entranceRed);
        $node = self::makeSection(3, $entranceRed);
        
        // close the cycle
        $entranceYellow->setNext($node);

        $this->startFields = [
            $startFieldsYellow,
            $startFieldsGreen,
            $startFieldsBlue,
            $startFieldsRed,
        ];
        
        // create pawns
        $this->pawns = [
        [
        new Pawn(0, $startFieldsYellow, 0),
        new Pawn(0, $startFieldsYellow, 1),
        new Pawn(0, $startFieldsYellow, 2),
        new Pawn(0, $startFieldsYellow, 3),
        ],
        [
        new Pawn(1, $startFieldsGreen, 0),
        new Pawn(1, $startFieldsGreen, 1),
        new Pawn(1, $startFieldsGreen, 2),
        new Pawn(1, $startFieldsGreen, 3),
        ],
        [
        new Pawn(2, $startFieldsBlue, 0),
        new Pawn(2, $startFieldsBlue, 1),
        new Pawn(2, $startFieldsBlue, 2),
        new Pawn(2, $startFieldsBlue, 3),
        ],
        [
        new Pawn(3, $startFieldsRed, 0),
        new Pawn(3, $startFieldsRed, 1),
        new Pawn(3, $startFieldsRed, 2),
        new Pawn(3, $startFieldsRed, 3),
        ],
        ];
    }

    public function movePawnToStart($pawn) {
        $pawn->field = $this->startFields[$pawn->player];
    }
    
    public function allPawnsHome($player) {
        foreach ($this->pawns[$player] as $index => $pawn) {
            if (is_a($pawn->field, 'HomeField') == false) {
                return false;
            }
        }
        return true;
    }

    public function allPawnsStart($player) {
        foreach ($this->pawns[$player] as $index => $pawn) {
            if (is_a($pawn->field, 'StartFields') == false) {
                return false;
            }
        }
        return true;
    }

    public function getPawnOnField($field) {
        for ($i=0; $i < 4 ; $i++) {
            foreach ($this->pawns[$i] as $index => $pawn) {
                if ($pawn->field === $field) {
                    return $pawn;
                } 
            }
        }
        return null;
    }
    
    public function finishedPlayers() {
        $finishedPlayers = [];
        for ($i=0; $i < 4 ; $i++) {
            $inHomes = 0;
            foreach ($this->pawns[$i] as $index => $pawn) {
                if (is_a($pawn->field, 'HomeField')) {
                    $inHomes++;
                }
            }
            if ($inHomes == 4) {
                array_push($finishedPlayers, $i);
            }
        }
        return $finishedPlayers;
    }
    
    public function getPossibleMoves($player, $dice) {
        $moves = [];
        foreach ($this->pawns[$player] as $index => $pawn) {
            // Is entrance occupied by own pawn
            if(is_a($pawn->field, 'EntranceField')) {
                $pawnMoves = $pawn->field->traverse($player, $dice);
                // TODO Check if entrance is occupied by own pawn
                // TODO Check if pawn on entrance can move
                // TODO If not, move the next pawn until the last one
                return [new Move($pawn, $pawnMoves[0])];
            }
            $pawnMoves = $pawn->field->traverse($player, $dice);
            if (count($pawnMoves) == 0) {
                continue;
            }
            // $pawnMoves is an array but possible number of elements is zero or one atm.
            $too = $pawnMoves[0];

            // has target field own pawn
            $pawnOnTarget = $this->getPawnOnField($too);
            if ($pawnOnTarget != null && $pawnOnTarget->player == $player) {
                continue;
            }

            $move = new Move($pawn, $too);
            array_push($moves, $move);
        }
        return $moves;
    }
}

?>