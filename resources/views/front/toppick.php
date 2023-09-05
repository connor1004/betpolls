<?php
  use App\BetType;
  use App\Vote;
  use App\Facades\Data;
  use App\Facades\Format;
  use App\Facades\Utils;
  use App\Facades\Options;
  $socials = Options::getSocialMediaLinkOption();
?>
<?php require_once(dirname(__FILE__) . '/partials/header.php'); ?>

<div class="container">
  <?php 
    $banner_settings = (object)[
      'banner_name' => 'top',
      'style' => 'display: inline-block; width: 100%; height: 90px;',
    ];
    require(dirname(__FILE__) . '/widgets/banner.php');
  ?>
  <div class="row">
    <div class="col-12">
      <div class="card widget top-picks-widget">
        <div class="card-header">
          <span><b><?php echo trans('app.todays_top_picks'); ?></b></span>
        </div>
        <div class="card-body">
          <table class="table table-hover">
            <tbody>
              <?php $rank = 1; ?>
              <?php if(count($picks) === 0) : ?>
                <td colspan="3" class="text-center">
                  <?php echo trans('app.no_results_found'); ?>
                </td>
              <?php else : ?>
                <?php foreach($picks as $top_pick) :
                  $top_pick_game_home_team_first = $top_pick->game_bet_type->game->between_players || $top_pick->game_bet_type->game->league->sport_category->home_team_first === 1;
                  $top_pick_game_bet_type = $top_pick->game_bet_type;
                  $top_pick_game = $top_pick_game_bet_type->game;
                  $top_pick_bet_type = $top_pick->game_bet_type->bet_type;
                  if ($top_pick_game_home_team_first) {
                    $top_pick_game_first = (object)[
                      'team' => $top_pick_game->home_team,
                      'weight' => 'weight_1',
                      'vote_case' => Vote::$VOTE_CASE_WIN
                    ];

                    $top_pick_game_second = (object)[
                      'team' => $top_pick_game->away_team,
                      'weight' => 'weight_2',
                      'vote_case' => Vote::$VOTE_CASE_LOSS
                    ];
                  } else {
                    $top_pick_game_first = (object)[
                      'team' => $top_pick_game->away_team,
                      'weight' => 'weight_2',
                      'vote_case' => Vote::$VOTE_CASE_LOSS
                    ];

                    $top_pick_game_second = (object)[
                      'team' => $top_pick_game->home_team,
                      'weight' => 'weight_1',
                      'vote_case' => Vote::$VOTE_CASE_WIN
                    ];
                  }
                ?>
                  <tr>
                    <td>
                      <a href="<?php echo $top_pick_game->league->url; ?>">
                        <b>#<?php echo $rank; ?></b>
                      </a>
                    </td>
                    <td>
                      <div class="team">
                        <div class="team-logo">
                          <img src="<?php echo $top_pick_game_first->team->logo; ?>">
                        </div>
                        <div class="team-name">
                          <span class="d-block d-lg-none">
                            <?php echo $top_pick_game_first->team->locale_short_name; ?>
                          </span>
                          <span class="d-none d-lg-block">
                            <?php echo $top_pick_game_first->team->locale_name; ?>
                          </span>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="team">
                        <div class="team-logo">
                          <img src="<?php echo $top_pick_game_second->team->logo; ?>" />
                        </div>
                        <div class="team-name">
                          <span class="d-block d-lg-none">
                            <?php echo $top_pick_game_second->team->locale_short_name; ?>
                          </span>
                          <span class="d-none d-lg-block">
                            <?php echo $top_pick_game_second->team->locale_name; ?>
                          </span>
                        </div>
                      </div>
                    </td>
                    <td>
                      <?php if ($top_pick_bet_type->value === BetType::$VALUE_SPREAD) :
                          $top_pick_game_selected = $top_pick->vote_case === $top_pick_game_first->vote_case ? $top_pick_game_first : $top_pick_game_second;
                          echo $top_pick_game_selected->team->locale_short_name;
                        ?>
                        <span>
                          <?php echo Format::formatSignedWeight($top_pick_game_bet_type[$top_pick_game_selected->weight]); ?>
                          <?php
                            $win = $top_pick_game_bet_type->game_vote->win_vote_count;
                            $loss = $top_pick_game_bet_type->game_vote->loss_vote_count;
                            $tie = $top_pick_game_bet_type->game_vote->tie_vote_count;

                            $sum = $win + $loss + $tie;

                            if ($top_pick->vote_case === Vote::$VOTE_CASE_WIN) {
                              $value = $win;
                            } elseif ($top_pick->vote_case === Vote::$VOTE_CASE_TIE) {
                              $value = $tie;
                            } else {
                              $value = $loss;
                            }
                            
                            echo ' | ' . round($value / $sum * 100);
                          ?>%
                          (<?php echo $value; ?>)
                        </span>
                      <?php elseif ($top_pick_bet_type->value === BetType::$VALUE_MONEYLINE) : 
                          if ($top_pick->vote_case === $top_pick_game_first->vote_case) {
                            $top_pick_game_weight = $top_pick_game_first->weight;
                            $top_pick_game_team = $top_pick_game_first->team->locale_short_name;
                          } elseif ($top_pick->vote_case === Vote::$VOTE_CASE_TIE) {
                            $top_pick_game_weight = 'weight_3';
                            $top_pick_game_team = trans('app.tie');
                          } else {
                            $top_pick_game_weight = $top_pick_game_second->weight;
                            $top_pick_game_team = $top_pick_game_second->team->locale_short_name;
                          }
                          echo $top_pick_game_team;
                        ?>
                        <span>
                          <?php echo Format::formatSignedWeight($top_pick_game_bet_type[$top_pick_game_weight]); ?>
                          <?php
                            $win = $top_pick_game_bet_type->game_vote->win_vote_count;
                            $loss = $top_pick_game_bet_type->game_vote->loss_vote_count;
                            $tie = $top_pick_game_bet_type->game_vote->tie_vote_count;

                            $sum = $win + $loss + $tie;

                            if ($top_pick->vote_case === Vote::$VOTE_CASE_WIN) {
                              $value = $win;
                            } elseif ($top_pick->vote_case === Vote::$VOTE_CASE_TIE) {
                              $value = $tie;
                            } else {
                              $value = $loss;
                            }
                            
                            echo ' | ' . round($value / $sum * 100);
                          ?>%
                          (<?php echo $value; ?>)
                        </span>
                      <?php elseif ($top_pick_bet_type->value === BetType::$VALUE_OVER_UNDER) : ?>
                        <div>
                          <?php if ($top_pick->vote_case === Vote::$VOTE_CASE_WIN) : ?>
                            <span class="text-success">↑</span>
                            <?php echo trans('app.over'); ?>
                            <?php echo $top_pick_game_bet_type->weight_1; ?>
                            <?php
                              $win = $top_pick_game_bet_type->game_vote->win_vote_count;
                              $loss = $top_pick_game_bet_type->game_vote->loss_vote_count;

                              echo ' | ' . round($win / ($win + $loss) * 100)
                            ?>%
                            (<?php echo $top_pick_game_bet_type->game_vote->win_vote_count; ?>)
                          <?php else : ?>
                            <span class="text-danger">↓</span>
                            <?php echo trans('app.under'); ?>
                            <?php echo $top_pick_game_bet_type->weight_1; ?>
                            <?php
                              $win = $top_pick_game_bet_type->game_vote->win_vote_count;
                              $loss = $top_pick_game_bet_type->game_vote->loss_vote_count;

                              echo ' | ' . round($loss / ($win + $loss) * 100)
                            ?>%
                            (<?php echo $top_pick_game_bet_type->game_vote->loss_vote_count; ?>)
                          <?php endif; ?>
                        </div>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php if ($rank % 10 == 0) : ?>
                    <tr>
                      <td colspan="4">
                        <?php
                          $banner_settings = (object)[
                            'banner_name' => 'middle'
                          ];
                          require(dirname(__FILE__) . '/widgets/banner.php');
                        ?>
                      </td>
                    </tr>
                  <?php endif; ?>
                <?php $rank++; endforeach; ?>
              <?php endif; ?>
              <?php if ($rank < 10) : ?>
                <tr>
                  <td colspan="4">
                    <?php
                      $banner_settings = (object)[
                        'banner_name' => 'middle'
                      ];
                      require(dirname(__FILE__) . '/widgets/banner.php');
                    ?>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-12">
      <?php if (isset($page)) : ?>
        <div class="mt-2">
          <?php echo $page->locale_content; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <?php 
    $banner_settings = (object)[
      'banner_name' => 'bottom',
      'style' => 'display: inline-block; width: 100%; height: 90px;',
    ];
    require(dirname(__FILE__) . '/widgets/banner.php');
  ?>
</div>

</div <?php require_once(dirname(__FILE__) . '/partials/footer.php'); ?>