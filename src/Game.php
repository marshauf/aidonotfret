<?php

require __DIR__ . '/Board.php';
require __DIR__ . '/Dice.php';

class Turn {
    public $number;
    public $player;
    public $actions;
}

class Action {
    public $dice;
    public $move;
    public $throw;
}

class Game {
    private $players;
    private $board;
    private $turn;
    private $dice;

    public function __construct($players) {
        $this->players = $players;
        $this->board = new Board();
        $this->turn = 0;
        // $apiKey = 'b9a77b3a-4325-4e7e-856f-948e3f0bce5a';
        // $this->dice = new Dice($apiKey);
        // INFO: api.random.org disabled due bit size limit reached
        $this->dice = new LocalDice();
    }

    public function finished() {
        $finishedPlayers = $this->board->finishedPlayers();
        return count($finishedPlayers) > 0;
    }

    public function getWinner() {
        $finishedPlayers = $this->board->finishedPlayers();
        return $this->players[$finishedPlayers[0]];
    }

    public function getPlayer($number) {
        return $this->players[$number];
    }

    public function playTurn() {
        $this->turn++;
        $playerNumber = $this->turn % count($this->players);
        $player = $this->players[$playerNumber];
        $turn = new Turn();
        $turn->number = $this->turn;
        $turn->player = $player;
        $turn->actions = [];
        
        $rollsLeft = 1;
        $allPawnsStart = false;
        if ($this->board->allPawnsStart($playerNumber)) {
            $rollsLeft = 3;
            $allPawnsStart = true;
        }
        while($rollsLeft > 0) {
            $rollsLeft--;
            $roll = $this->dice->roll();

            // Report
            $action = new Action();
            $action->dice = $roll;

            if ($roll == 6) {
                if ($allPawnsStart) {
                    $allPawnsStart = false;
                    $rollsLeft = 0;
                }
                $rollsLeft++;
            }
            $moves = $this->board->getPossibleMoves($playerNumber, $roll);
            if (count($moves) !== 0) {
                $decidedMove = $player->turn($moves);
                $pawnOnTarget = $this->board->getPawnOnField($decidedMove->target);
                if ($pawnOnTarget != null) {
                    $this->board->movePawnToStart($pawnOnTarget);
                }
                $decidedMove->pawn->field = $decidedMove->target;
                
                // Report
                $action->throw = $pawnOnTarget;
                $action->move = $decidedMove;

                // array_push($turn->actions, $action);
            }
            // Report
            array_push($turn->actions, $action);
        }
        return $turn;
    }
}

?>
