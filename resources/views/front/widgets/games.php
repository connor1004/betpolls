<?php 
  use Carbon\Carbon;
  use App\BetType;
  use App\Game;
  use App\Vote;
  use App\Facades\Format;
  use App\Facades\Geoip;
?>
<div class="card widget games-widget">
  <div class="card-header">
    <div>
      <b><?php echo $games_settings->title; ?></b>
    </div>
  </div>
  <div class="card-body">
    <table class="table table-bordered">
      <thead>
        <td colspan="4">
          <select class="select2 form-control" name="period_type" onchange="window.open(this.value, '_self');">
            <option value=""><?php echo trans('app.select_a_league'); ?></option>
            <?php foreach($games_settings->leagues as $games_settings_league) : ?>
              <option value="<?php echo $games_settings_league->locale_url; ?>">
                <?php echo $games_settings_league->locale_name; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </td>
      </thead>
      <tbody>
        <?php if($games_settings->games->count() === 0) : ?>
          <td colspan="4" class="text-center">
            <?php echo trans('app.no_results_found'); ?>
          </td>
        <?php else : ?>
          <?php foreach($games_settings->games as $games_settings_game) : 
            $games_settings_game_available_game_bet_types = $games_settings_game->available_game_bet_types;
            $games_settings_game_home_team_first = $games_settings_game->between_players || ($games_settings_game->league->sport_category->home_team_first === 1);

            
            if ($games_settings_game_home_team_first) {
              $games_settings_game_first = (object)[
                'team' => $games_settings_game->home_team,
                'general_info' => isset($games_settings_game->game_general_info['hometeam']) ? $games_settings_game->game_general_info['hometeam'] : null,
                'score' => $games_settings_game->home_team_score,
                'vote_case' => Vote::$VOTE_CASE_WIN,
                'weight' => 'weight_1'
              ];
              $games_settings_game_second = (object)[
                'team' => $games_settings_game->away_team,
                'general_info' => isset($games_settings_game->game_general_info['awayteam']) ? $games_settings_game->game_general_info['awayteam'] : null,
                'score' => $games_settings_game->away_team_score,
                'vote_case' => Vote::$VOTE_CASE_LOSS,
                'weight' => 'weight_2',
              ];
            } else {
              $games_settings_game_first = (object)[
                'team' => $games_settings_game->away_team,
                'general_info' => isset($games_settings_game->game_general_info['awayteam']) ? $games_settings_game->game_general_info['awayteam'] : null,
                'score' => $games_settings_game->away_team_score,
                'vote_case' => Vote::$VOTE_CASE_LOSS,
                'weight' => 'weight_2'
              ];
              $games_settings_game_second = (object)[
                'team' => $games_settings_game->home_team,
                'general_info' => isset($games_settings_game->game_general_info['hometeam']) ? $games_settings_game->game_general_info['hometeam'] : null,
                'score' => $games_settings_game->home_team_score,
                'vote_case' => Vote::$VOTE_CASE_WIN,
                'weight' => 'weight_1',
              ];
            }
            $game_settings_game_url = "{$games_settings_game->league->locale_url}#game_{$games_settings_game_first->team->id}_{$games_settings_game_second->team->id}";
          ?>
            <tr>
              <td colspan="4">
                <div class="d-flex justify-content-between align-items-center">
                  <b>
                    <?php switch ($games_settings_game->status) :
                      case Game::$STATUS_NOT_STARTED:
                        // $geoip = Geoip::getGeoip();
                        // echo Carbon::createFromFormat('Y-m-d H:i:s', $games_settings_game->start_at, 'UTC')->setTimezone($geoip ? $geoip->time_zone : 'UTC')->format('g:i A');
                        // if (!$geoip) {
                        //   echo ' UTC';
                        // }
                        echo Geoip::getLocalizedDate($games_settings_game->start_at, 'time');
                        break;
                      case Game::$STATUS_STARTED:
                        printf("%s, %s %d-%d", trans('app.live'), $games_settings_game->game_info['status'], $games_settings_game_first->score, $games_settings_game_second->score);
                        break;
                      case Game::$STATUS_POSTPONED:
                        echo trans('app.postponed');
                        break;
                      case Game::$STATUS_ENDED:
                        printf("%s, %d-%d", trans('app.final'), $games_settings_game_first->score, $games_settings_game_second->score);
                        break;
                    endswitch ?>
                  </b>
                    <?php if ($games_settings_game->status === Game::$STATUS_NOT_STARTED || $games_settings_game->status === Game::$STATUS_POSTPONED) :
                      if (count($games_settings_game_available_game_bet_types) > 0) : ?>
                        <a class="btn btn-success btn-sm" href="<?php echo $game_settings_game_url; ?>">
                          <?php echo trans('app.vote'); ?>
                        </a>
                      <?php else : ?>
                        <a class="btn btn-secondary btn-sm" href="<?php echo $game_settings_game_url; ?>">
                          <?php echo trans('app.show'); ?>
                        </a>
                      <?php endif; ?>
                    <?php else: ?>
                      <a class="btn btn-primary btn-sm" href="<?php echo $game_settings_game_url; ?>">
                        <?php echo trans('app.results'); ?>
                      </a>
                    <?php endif; ?>
                </div>
              </td>
            </tr>
            <tr>
              <td class="w-100" colspan="<?php echo (4 - count($games_settings_game_available_game_bet_types)); ?>">
                <div class="team home-team">
                  <div class="team-logo">
                    <img src="<?php echo $games_settings_game_first->team->logo; ?>">
                  </div>
                  <div class="team-name">
                    <?php echo $games_settings_game_first->team->locale_short_name; ?>
                  </div>
                </div>
                <div class="team away-team">
                  <div class="team-logo">
                    <img src="<?php echo $games_settings_game_second->team->logo; ?>">
                  </div>
                  <div class="team-name">
                    <?php echo $games_settings_game_second->team->locale_short_name; ?>
                  </div>
                </div>
              </td>
              <?php foreach($games_settings_game_available_game_bet_types as $games_settings_game_available_game_bet_type) : ?>
                <td class="text-center align-middle">
                  <?php if ($games_settings_game_available_game_bet_type->bet_type->value === BetType::$VALUE_SPREAD) : ?>
                    <div class="text-center<?php echo $games_settings_game_available_game_bet_type->match_case === $games_settings_game_first->vote_case ? " text-success": "" ?>">
                      <?php echo Format::formatSignedWeight($games_settings_game_available_game_bet_type[$games_settings_game_first->weight]); ?>
                    </div>
                    <hr />
                    <div class="text-center<?php echo $games_settings_game_available_game_bet_type->match_case === $games_settings_game_second->vote_case ? " text-success": "" ?>">
                      <?php echo Format::formatSignedWeight($games_settings_game_available_game_bet_type[$games_settings_game_second->weight]); ?>
                    </div>
                  <?php elseif ($games_settings_game_available_game_bet_type->bet_type->value === BetType::$VALUE_MONEYLINE) :  ?>
                    <div class="text-center<?php echo $games_settings_game_available_game_bet_type->match_case === $games_settings_game_first->vote_case ? " text-success": "" ?>">
                      <?php echo Format::formatSignedWeight($games_settings_game_available_game_bet_type[$games_settings_game_first->weight]); ?>
                    </div>
                    <hr />
                    <div class="text-center<?php echo $games_settings_game_available_game_bet_type->match_case === $games_settings_game_second->vote_case ? " text-success": "" ?>">
                      <?php echo Format::formatSignedWeight($games_settings_game_available_game_bet_type[$games_settings_game_second->weight]); ?>
                    </div>
                  <?php else : ?>
                    <div class="<?php echo $games_settings_game_available_game_bet_type->match_case === Vote::$VOTE_CASE_WIN ? 'game-bet-over' : ($games_settings_game_available_game_bet_type->match_case === Vote::$VOTE_CASE_LOSS ? 'game-bet-under' : '')  ?>">
                      <?php echo $games_settings_game_available_game_bet_type->weight_1; ?>
                    </div>
                  <?php endif; ?>
                </td>
              <?php endforeach; ?>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>