<?php
  use Carbon\Carbon;
  use App\Game;
  use App\Vote;
  use App\Facades\Geoip;
  $votes = [];
  $home_team_first = $game->league->sport_category->home_team_first === 1;
  $between_players = $game->between_players;
  $home_scores = [];
  $away_scores = [];
  if ($between_players && $game->game_info) {
    if ($game->game_info['hometeam'] && $game->game_info['hometeam']['score']) {
      foreach ($game->game_info['hometeam']['score'] as $score_item) {
        $score_elements = explode('.', $score_item);
        $element_score = $score_elements[0];
        // if (count($score_elements) > 1) {
        //   $element_score .= ' ('.$score_elements[1].')';
        // }
        $home_scores[] = $element_score;
      }
    }
    if ($game->game_info['awayteam'] && $game->game_info['awayteam']['score']) {
      foreach ($game->game_info['awayteam']['score'] as $score_item) {
        $score_elements = explode('.', $score_item);
        $element_score = $score_elements[0];
        // if (count($score_elements) > 1) {
        //   $element_score .= ' ('.$score_elements[1].')';
        // }
        $away_scores[] = $element_score;
      }
    }
  }
  if ($home_team_first || $between_players) {
    $first = (object)[
      'key' => 'home_team',
      'team' => $game->home_team,
      'general_info' => isset($game->game_general_info['hometeam']) ? $game->game_general_info['hometeam'] : null,
      'score' => $game->home_team_score,
      'scores' => $home_scores,
      'weight' => 'weight_1',
      'vote_case' => Vote::$VOTE_CASE_WIN,
      'vote_percent' => 'win_vote_percent',
      'vote_count' => 'win_vote_count'
    ];
    $second = (object)[
      'key' => 'away_team',
      'team' => $game->away_team,
      'general_info' => isset($game->game_general_info['awayteam']) ? $game->game_general_info['awayteam'] : null,
      'score' => $game->away_team_score,
      'scores' => $away_scores,
      'weight' => 'weight_2',
      'vote_case' => Vote::$VOTE_CASE_LOSS,
      'vote_percent' => 'loss_vote_percent',
      'vote_count' => 'loss_vote_count'
    ];
  } else {
    $first = (object)[
      'key' => 'away_team',
      'team' => $game->away_team,
      'general_info' => isset($game->game_general_info['awayteam']) ? $game->game_general_info['awayteam'] : null,
      'score' => $game->away_team_score,
      'scores' => $away_scores,
      'weight' => 'weight_2',
      'vote_case' => Vote::$VOTE_CASE_LOSS,
      'vote_percent' => 'loss_vote_percent',
      'vote_count' => 'loss_vote_count'
    ];
    $second = (object)[
      'key' => 'home_team',
      'team' => $game->home_team,
      'general_info' => isset($game->game_general_info['hometeam']) ? $game->game_general_info['hometeam'] : null,
      'score' => $game->home_team_score,
      'scores' => $home_scores,
      'weight' => 'weight_1',
      'vote_case' => Vote::$VOTE_CASE_WIN,
      'vote_percent' => 'win_vote_percent',
      'vote_count' => 'win_vote_count'
    ];
  }
  $available_game_bet_types = $game->available_game_bet_types;
  $game_status = $game->status;
  if ($game->status == Game::$STATUS_STARTED && $game->game_info['status'] == 'Postponed') {
    $game_status = Game::$STATUS_POSTPONED;
  }
?>
<app-game
  class="game-item<?php echo isset($active) ? " active" : ""; ?>"
  id="<?php echo "game_{$first->team->id}_{$second->team->id}"; ?>">
  <table class="table game-main">
    <tr class="game-display">
      <td class="game-start-time" colspan="5">
        <?php switch ($game_status) :
          case Game::$STATUS_NOT_STARTED:
            // $geoip = Geoip::getGeoip();
            // echo Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at, 'UTC')->setTimezone($geoip ? $geoip->time_zone : 'UTC')->format('g:i A');
            // if (!$geoip) {
            //   echo ' UTC';
            // }
            echo Geoip::getLocalizedDate($game->start_at, 'time');
            break;
          case Game::$STATUS_STARTED:
            if ($game->setting_manually) {
              printf("%s", trans('app.live'));
            }
            else {
              printf("%s : %s", trans('app.live'), $game->game_info['status']);
            }
            break;
          case Game::$STATUS_POSTPONED:
            echo trans('app.postponed');
            break;
          case Game::$STATUS_ENDED:
            printf("%s", trans('app.final'));
            break;
        endswitch ?>
        <?php if (isset($vote_error)) : ?>
          <span class="vote-error"><?php echo $vote_error; ?></span>
        <?php endif; ?>
      </td>
    </tr>
    <?php
      $game_team = $first;
      $is_first = true;
      $is_winner = $first->score > $second->score ? true: false;
      $show_scores = $game->between_players;
      require(dirname(__FILE__) . '/game-team.php');
      
      $game_team = $second;
      $is_first = false;
      $is_winner = $first->score < $second->score ? true: false;
      $show_scores = $game->between_players;
      require(dirname(__FILE__) . '/game-team.php');
    ?>
    <tr class="game-actions">
      <td colspan="5">
        <?php if ($game->available_game_bet_types->count() > 0) : ?>
          <?php if (($game->status !== Game::$STATUS_NOT_STARTED && $game->status !== Game::$STATUS_POSTPONED)
           || $game->stop_voting || $game->start_at < (new Carbon())->format('Y-m-d H:i:s')) : ?>
            <button type="button" class="btn btn-primary game-show-more"><?php echo trans('app.results'); ?></button>
          <?php else : ?>
            <?php if ($user) :  
              $votes = Vote::where(['user_id' => $user->id, 'game_id' => $game->id])->get();
              if (count($votes) > 0) : ?>
                <button type="button" class="btn btn-primary game-show-more"><?php echo trans('app.results'); ?></button>
              <?php else : ?>
                <button type="button" class="btn btn-success game-show-more"><?php echo trans('app.vote'); ?></button>
              <?php endif; ?>
            <?php else : ?>
              <a href="<?php echo url('login').'?redirect='.$league->url; ?>" class="btn btn-success"><?php echo trans('app.vote'); ?></a>
            <?php endif; ?>
          <?php endif; ?>
        <?php endif; ?>
      </td>
    </tr>
  </table>
  <div class="game-details">
    <?php require(dirname(__FILE__) . '/game-bettings.php'); ?>
  </div>
</app-game>