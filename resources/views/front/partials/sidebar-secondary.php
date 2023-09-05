<?php
  use App\Facades\Data;
  
  $banner_settings = (object)[
    'banner_name' => 'side',
    'style' => 'display: inline-block; width: 100%; height: 320px;',
  ];
  require(dirname(__FILE__) . '/../widgets/banner.php');
  
  if (empty($not_show_leaderboard)) {
    $leaderboard_settings = (object)[
      'title' => trans('app.leaderboard'),
      'leaderboards' => Data::getLeaderBoards()
    ];
    require(dirname(__FILE__) . '/../widgets/leaderboard.php');
  }

  $games_settings = (object)[
    'title' => trans('app.games_of_today'),
    'leagues' => Data::getLeagues(),
    'games' => Data::getGames()
  ];
  require(dirname(__FILE__) . '/../widgets/games.php');