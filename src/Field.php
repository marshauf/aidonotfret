<?php

class Field {
    protected $next;

    public function __construct($nextField) {
		$this->next = $nextField;
	}

    public function traverse($player, $dice) {
        if ($dice == 0) {
            return [$this];
        }
        return $this->next->traverse($player, $dice - 1);
    }
}

class StartFields {
    private $entrance;

    public function __construct($entranceField) {
		$this->entrance = $entranceField;
	}

    // Only allows dices with the number 6
    public function traverse($player, $dice) {
        if ($dice == 6) {
            return [$this->entrance];
        }
        return [];
    }
}

class EntranceField extends Field {
    public function __construct($nextField) {
		parent::__construct($nextField);
	}

    public function setNext($nextField) {
        $this->next = $nextField;
    }
}

class ExitField {
    private $next;
    private $home;
    private $player;

    public function __construct($next, $home, $player) {
		$this->next = $next;
        $this->home = $home;
        $this->player = $player;
	}

    public function traverse($player, $dice) {
        if ($dice == 0) {
            return [$this];
        }
        // Is this exit for the traversing player
        if ($this->player == $player) {
            // traverse to home
            return $this->home->traverse($player, $dice - 1);
        }
        return $this->next->traverse($player, $dice - 1);
    }
}

class HomeField {
    private $nextHome;

    public function __construct($nextHome = null) {
		$this->nextHome = $nextHome;
	}

    public function traverse($player, $dice) {
        if ($dice == 0) {
            return [$this];
        }
        if ($this->nextHome == null) {
            return [];
        }
        return $this->nextHome->traverse($player, $dice - 1);
    }
}

?>