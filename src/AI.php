<?php

class AI {
    private $name;

    public function __construct($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function turn($moves) {
        return $moves[0];
    }
}

interface iPlayer {
    public function getName();
    public function turn($moves);
}

?>