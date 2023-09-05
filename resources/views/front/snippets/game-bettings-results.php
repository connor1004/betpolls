<?php
  use App\BetType;
  use App\Vote;
  use App\Facades\Format;

  // $is_tennis = $game->league && $game->league->sport_category && $game->league->sport_category->name == "Tennis";
?>
<div class="game-bettings game-bettings-results">
  <div class="row">
    <?php $game_bet_types = $game->game_bet_types;
      $game_bet_type_index = 0;
      foreach($game_bet_types as $game_bet_type) : 
        $bet_type = $game_bet_type->bet_type;
        $game_vote = $game_bet_type->game_vote;
        $vote = null;
        if ($user) {
          $vote = Vote::where(['game_bet_type_id' => $game_bet_type->id, 'user_id' => $user->id])->first();
        }
        if (!empty($game_bet_type->weight_1) && (!$game->is_nulled || $bet_type->id == 2)) :
    ?>
      <div class="col-12 col-md-6 col-xl-4 mb-2">
        <div class="game-betting">
          <!-- GAME BET TYPE ID -->

          <div class="game-betting-title">
            <?php echo $bet_type->name; ?>
            <?php if ($bet_type->value === BetType::$VALUE_OVER_UNDER) : ?>
              (<?php echo $game_bet_type->weight_1; ?>)
            <?php endif; ?>
          </div>

          <!-- SPREAD -->
          <?php if ($bet_type->value === BetType::$VALUE_SPREAD) : ?>
            <div class="game-betting-case">
              <div class="case-title">
                <?php echo $first->team->locale_short_name; ?>
                <?php echo Format::formatSignedWeight($game_bet_type[$first->weight]); ?>
              </div>
              <div class="case-value">
                <?php
                  $game_voted_progress = (object)[
                    'percent' => $game_vote ? $game_vote[$first->vote_percent] : 0,
                    'count' => $game_vote ? $game_vote[$first->vote_count] : 0,
                  ];
                  require(dirname(__FILE__) . '/game-voted-progress.php');
                ?>
              </div>
            </div>
            <div class="game-betting-case">
              <div class="case-title">
                <?php echo $second->team->locale_short_name; ?>
                <?php echo Format::formatSignedWeight($game_bet_type[$second->weight]); ?>
              </div>
              <div class="case-value">
                <?php
                  $game_voted_progress = (object)[
                    'percent' => $game_vote ? $game_vote[$second->vote_percent] : 0,
                    'count' => $game_vote ? $game_vote[$second->vote_count] : 0,
                  ];
                  require(dirname(__FILE__) . '/game-voted-progress.php');
                ?>
              </div>
            </div>
          
          <!-- MONEY LINE -->
          <?php elseif ($bet_type->value === BetType::$VALUE_MONEYLINE) : ?>
            <div class="game-betting-case">
              <div class="case-title">
                <?php echo $first->team->short_name; ?>
                <?php echo Format::formatSignedWeight($game_bet_type[$first->weight]); ?>
              </div>
              <div class="case-value">
                <?php
                  $game_voted_progress = (object)[
                    'percent' => $game_vote ? $game_vote[$first->vote_percent] : 0,
                    'count' => $game_vote ? $game_vote[$first->vote_count] : 0,
                  ];
                  require(dirname(__FILE__) . '/game-voted-progress.php');
                ?>
              </div>
            </div>
            <div class="game-betting-case">
              <div class="case-title">
                <?php echo $second->team->short_name; ?>
                <?php echo Format::formatSignedWeight($game_bet_type[$second->weight]); ?>
              </div>
              <div class="case-value">
                <?php
                  $game_voted_progress = (object)[
                    'percent' => $game_vote ? $game_vote[$second->vote_percent] : 0,
                    'count' => $game_vote ? $game_vote[$second->vote_count] : 0,
                  ];
                  require(dirname(__FILE__) . '/game-voted-progress.php');
                ?>
              </div>
            </div>
            
            <?php if ($game_bet_type->weight_3 > 0) : ?>
              <div class="game-betting-case">
                <div class="case-title">
                  <?php echo trans('app.tie') ?>
                  <?php echo Format::formatSignedWeight($game_bet_type->weight_3); ?>
                </div>
                <div class="case-value">
                  <?php
                    $game_voted_progress = (object)[
                      'percent' => $game_vote ? $game_vote->tie_vote_percent : 0,
                    'count' => $game_vote ? $game_vote->tie_vote_count : 0,
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
                    'percent' => $game_vote ? $game_vote->win_vote_percent : 0,
                    'count' => $game_vote ? $game_vote->win_vote_count : 0,
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
                    'percent' => $game_vote ? $game_vote->loss_vote_percent : 0,
                    'count' => $game_vote ? $game_vote->loss_vote_count : 0,
                  ];
                  require(dirname(__FILE__) . '/game-voted-progress.php');
                ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php $game_bet_type_index++; endif; endforeach ?>
  </div>
</div>