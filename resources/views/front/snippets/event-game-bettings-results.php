<?php
  use App\BetType;
  use App\ManualEventVote;
  use App\Facades\Format;
?>
<div class="game-bettings game-bettings-results">
  <div class="row">
    <?php $event_bet_types = $poll_content['bet_types'];
      $event_bet_type_index = 0;
      foreach($event_bet_types as $event_bet_type) : 
        $bet_type = $event_bet_type->bet_type;
        $vote = null;
        if ($user) {
          $vote = ManualEventVote::where(['event_bet_type_id' => $event_bet_type->id, 'user_id' => $user->id])->first();
        }
    ?>
      <div class="col-12 col-md-6 col-xl-4 mb-2">
        <div class="game-betting">
          <!-- GAME BET TYPE ID -->

          <div class="game-betting-title">
            <?php echo $bet_type->name; ?>
            <?php if ($bet_type->value === BetType::$VALUE_OVER_UNDER) : ?>
              (<?php echo $poll->over_under; ?>)
            <?php endif; ?>
          </div>

          <!-- SPREAD -->
          <?php if ($bet_type->value === BetType::$VALUE_SPREAD) : ?>
            <div class="game-betting-case">
              <div class="case-title">
                <?php echo $poll->candidate1->locale_short_name; ?>
                <?php echo Format::formatSignedWeight($poll->spread); ?>
              </div>
              <div class="case-value">
                <?php
                  $game_voted_progress = (object)[
                    'percent' => $event_bet_type->win_vote_percent,
                    'count' => $event_bet_type->win_vote_count,
                  ];
                  require(dirname(__FILE__) . '/game-voted-progress.php');
                ?>
              </div>
            </div>
            <div class="game-betting-case">
              <div class="case-title">
                <?php echo $poll->candidate2->locale_short_name; ?>
                <?php echo Format::formatSignedWeight(-$poll->spread); ?>
              </div>
              <div class="case-value">
                <?php
                  $game_voted_progress = (object)[
                    'percent' => $event_bet_type->loss_vote_percent,
                    'count' => $event_bet_type->loss_vote_count,
                  ];
                  require(dirname(__FILE__) . '/game-voted-progress.php');
                ?>
              </div>
            </div>
          
          <!-- MONEY LINE -->
          <?php elseif ($bet_type->value === BetType::$VALUE_MONEYLINE) : ?>
            <div class="game-betting-case">
              <div class="case-title">
                <?php echo $poll->candidate1->locale_short_name; ?>
                <?php echo Format::formatSignedWeight($poll->candidate1_odds); ?>
              </div>
              <div class="case-value">
                <?php
                  $game_voted_progress = (object)[
                    'percent' => $event_bet_type->win_vote_percent,
                    'count' => $event_bet_type->win_vote_count,
                  ];
                  require(dirname(__FILE__) . '/game-voted-progress.php');
                ?>
              </div>
            </div>
            <div class="game-betting-case">
              <div class="case-title">
                <?php echo $poll->candidate2->locale_short_name; ?>
                <?php echo Format::formatSignedWeight($poll->candidate2_odds); ?>
              </div>
              <div class="case-value">
                <?php
                  $game_voted_progress = (object)[
                    'percent' => $event_bet_type->loss_vote_percent,
                    'count' => $event_bet_type->loss_vote_count,
                  ];
                  require(dirname(__FILE__) . '/game-voted-progress.php');
                ?>
              </div>
            </div>
            
            <?php if ($poll->tie_odds != 0) : ?>
              <div class="game-betting-case">
                <div class="case-title">
                  Tie
                  <?php echo Format::formatSignedWeight($poll->tie_odds); ?>
                </div>
                <div class="case-value">
                  <?php
                    $game_voted_progress = (object)[
                      'percent' => $event_bet_type->tie_vote_percent,
                      'count' => $event_bet_type->tie_vote_count,
                    ];
                    require(dirname(__FILE__) . '/game-voted-progress.php');
                  ?>
                </div>
              </div>
            <?php endif; ?>

          <!-- OVER/UNDER -->
          <?php elseif ($bet_type->value === BetType::$VALUE_OVER_UNDER) : ?>
            <div class="game-betting-case">
              <div class="case-title">
                <?php echo trans('app.over'); ?>&nbsp;<span class="text-success">↑</span>
              </div>
              <div class="case-value">
                <?php
                  $game_voted_progress = (object)[
                    'percent' => $event_bet_type->win_vote_percent,
                    'count' => $event_bet_type->win_vote_count,
                  ];
                  require(dirname(__FILE__) . '/game-voted-progress.php');
                ?>
              </div>
            </div>
            <div class="game-betting-case">
              <div class="case-title">
                <?php echo trans('app.under'); ?>&nbsp;<span class="text-danger">↓</span>
              </div>
              <div class="case-value">
                <?php
                  $game_voted_progress = (object)[
                    'percent' => $event_bet_type->loss_vote_percent,
                    'count' => $event_bet_type->loss_vote_count,
                  ];
                  require(dirname(__FILE__) . '/game-voted-progress.php');
                ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php $event_bet_type_index++; endforeach ?>
  </div>
</div>