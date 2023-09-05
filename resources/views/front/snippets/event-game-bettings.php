<?php
  use Carbon\Carbon;
  use App\ManualPollPage;
  use App\Vote;
  $event_bet_types = $poll->bet_types;
  
  if ($poll_page->start_at < (new Carbon())->format('Y-m-d H:i:s')) {
    require(dirname(__FILE__) . '/event-game-bettings-results.php');
  } elseif ($poll_page->status === ManualPollPage::$STATUS_NOT_STARTED) {
    if (count($votes) > 0) require(dirname(__FILE__) . '/event-game-bettings-results.php');
    else require(dirname(__FILE__) . '/event-game-bettings-unvoted.php');
  } elseif ($poll_page->status === ManualPollPage::$STATUS_STARTED) {
    if (count($votes) > 0) require(dirname(__FILE__) . '/event-game-bettings-results.php');
    else require(dirname(__FILE__) . '/event-game-bettings-results.php');
  } else {
    if (count($votes) > 0) require(dirname(__FILE__) . '/event-game-bettings-results.php');
    else require(dirname(__FILE__) . '/event-game-bettings-results.php');
  }