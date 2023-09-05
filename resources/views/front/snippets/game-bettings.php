<?php
  use Carbon\Carbon;
  use App\Game;
  use App\Vote;
  $game_bet_types = $game->game_bet_types;
  
  if ($game->stop_voting || $game->start_at < (new Carbon())->format('Y-m-d H:i:s')) {
    require(dirname(__FILE__) . '/game-bettings-results.php');
  } elseif ($game->status === Game::$STATUS_NOT_STARTED || $game->status === Game::$STATUS_POSTPONED) {
    if (count($votes) > 0) require(dirname(__FILE__) . '/game-bettings-results.php');
    else require(dirname(__FILE__) . '/game-bettings-unvoted.php');
  } elseif ($game->status === Game::$STATUS_STARTED) {
    if (count($votes) > 0) require(dirname(__FILE__) . '/game-bettings-results.php');
    else require(dirname(__FILE__) . '/game-bettings-results.php');
  } else {
    if (count($votes) > 0) require(dirname(__FILE__) . '/game-bettings-results.php');
    else require(dirname(__FILE__) . '/game-bettings-results.php');
  }