<html>
<body>
<?php
    require __DIR__ . '/Game.php';
    require __DIR__ . '/AI.php';

    $players = [new AI("Bob"), new AI("Alice"), new AI("Sarah"), new AI("Rob")];
    $game = new Game($players);
    $count = 1;
    while($game->finished() == false && $count < 3000) {
        $turn = $game->playTurn();
        echo '<div>';
        echo sprintf('%d: player:%s', $turn->number, $turn->player->getName());
        foreach ($turn->actions as $index => $action) {
            echo sprintf(' dice:%d', $action->dice);
            if ($action->move != null) {
                echo sprintf(' move pawn %d', $action->move->pawn->id);
            }
            if ($action->throw != null) {
                $thrownPlayerNumber = $action->throw->player;
                $thrownPlayer = $game->getPlayer($thrownPlayerNumber);
                echo sprintf(' player %s throws pawn %d of player %s', $turn->player->getName(), $action->throw->id, $thrownPlayer->getName());
            }
        }
        echo '</div>';
        $count++;
    }
    if ($game->finished()) {
        $winner = $game->getWinner();
        echo sprintf('<div>player %s won</div>', $winner->getName());
    } else {
        echo '<div>game was not finished</div>';
    }
?>
</body>
</html>