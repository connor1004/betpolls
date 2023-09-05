<?php
  use App\BetType;
  use App\Vote;
  use App\Facades\Format;

  // $is_tennis = $game->league && $game->league->sport_category && $game->league->sport_category->name == "Tennis";
?>
<div class="game-bettings game-bettings-unvoted">
  <form action="<?php echo url('games', [$game->id, 'vote']); ?>" method="post">
    <div class="row">
      <?php $game_bet_types = $game->game_bet_types;
        $game_bet_type_index = 0;
        foreach($game_bet_types as $game_bet_type) : 
          $bet_type = $game_bet_type->bet_type;
          if (!empty($game_bet_type->weight_1) && (!$game->is_nulled || $bet_type->id == 2)) :
      ?>
        <div class="col-12 col-md-6 col-xl-4 mb-2">
          <div class="game-betting"> 
            <!-- GAME BET TYPE ID -->
            <input
              type="hidden"
              name="<?php echo "votes[{$game_bet_type_index}][game_bet_type_id]"; ?>"
              value="<?php echo $game_bet_type->id; ?>"
            >

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
                <div class="case-value custom-control custom-radio">
                  <input
                    type="radio"
                    class="custom-control-input"
                    id="<?php echo "vote_case_{$game_bet_type->id}_{$game_bet_type_index}_{$first->vote_case}"; ?>"
                    name="<?php echo "votes[{$game_bet_type_index}][vote_case]" ?>"
                    value="<?php echo $first->vote_case; ?>"
                  >
                  <label
                    class="custom-control-label"
                    for="<?php echo "vote_case_{$game_bet_type->id}_{$game_bet_type_index}_{$first->vote_case}"; ?>">
                  </label>
                </div>
              </div>
              <div class="game-betting-case">
                <div class="case-title">
                  <?php echo $second->team->locale_short_name; ?>
                  <?php echo Format::formatSignedWeight($game_bet_type[$second->weight]); ?>
                </div>
                <div class="case-value custom-control custom-radio">
                  <input
                    type="radio"
                    class="custom-control-input"
                    id="<?php echo "vote_case_{$game_bet_type->id}_{$game_bet_type_index}_{$second->vote_case}"; ?>"
                    name="<?php echo "votes[{$game_bet_type_index}][vote_case]" ?>"
                    value="<?php echo $second->vote_case; ?>"
                  >
                  <label
                    class="custom-control-label"
                    for="<?php echo "vote_case_{$game_bet_type->id}_{$game_bet_type_index}_{$second->vote_case}"; ?>">
                  </label>
                </div>
              </div>
            
            <!-- MONEY LINE -->
            <?php elseif ($bet_type->value === BetType::$VALUE_MONEYLINE) : ?>
              <div class="game-betting-case">
                <div class="case-title">
                  <?php echo $first->team->short_name; ?>
                  <?php echo Format::formatSignedWeight($game_bet_type[$first->weight]); ?>
                </div>
                <div class="case-value custom-control custom-radio">
                  <input
                    type="radio"
                    class="custom-control-input"
                    id="<?php echo "vote_case_{$game_bet_type->id}_{$game_bet_type_index}_{$first->vote_case}"; ?>"
                    name="<?php echo "votes[{$game_bet_type_index}][vote_case]" ?>"
                    value="<?php echo $first->vote_case; ?>"
                  >
                  <label
                    class="custom-control-label"
                    for="<?php echo "vote_case_{$game_bet_type->id}_{$game_bet_type_index}_{$first->vote_case}"; ?>">
                  </label>
                </div>
              </div>
              <div class="game-betting-case">
                <div class="case-title">
                  <?php echo $second->team->short_name; ?>
                  <?php echo Format::formatSignedWeight($game_bet_type[$second->weight]); ?>
                </div>
                <div class="case-value custom-control custom-radio">
                  <input
                    type="radio"
                    class="custom-control-input"
                    id="<?php echo "vote_case_{$game_bet_type->id}_{$game_bet_type_index}_{$second->vote_case}"; ?>"
                    name="<?php echo "votes[{$game_bet_type_index}][vote_case]" ?>"
                    value="<?php echo $second->vote_case; ?>"
                  >
                  <label
                    class="custom-control-label"
                    for="<?php echo "vote_case_{$game_bet_type->id}_{$game_bet_type_index}_{$second->vote_case}"; ?>">
                  </label>
                </div>
              </div>

              <?php if (!empty($game_bet_type->weight_3)) : ?>
                <div class="game-betting-case">
                  <div class="case-title">
                    <?php echo trans('app.tie') ?>
                    <?php echo Format::formatSignedWeight($game_bet_type->weight_3); ?>
                  </div>
                  <div class="case-value custom-control custom-radio">
                    <input
                      type="radio"
                      class="custom-control-input"
                      id="<?php echo "vote_case_{$game_bet_type->id}_{$game_bet_type_index}_tie"; ?>"
                      name="<?php echo "votes[{$game_bet_type_index}][vote_case]" ?>"
                      value="<?php echo Vote::$VOTE_CASE_TIE; ?>"
                    >
                    <label
                      class="custom-control-label"
                      for="<?php echo "vote_case_{$game_bet_type->id}_{$game_bet_type_index}_tie"; ?>">
                    </label>
                  </div>
                </div>
              <?php endif; ?>

            <!-- OVER/UNDER -->
            <?php elseif ($bet_type->value === BetType::$VALUE_OVER_UNDER) : ?>
              <div class="game-betting-case">
                <div class="case-title">
                  <span class="text-success">↑</span>&nbsp;<?php echo trans('app.over'); ?>
                </div>
                <div class="case-value custom-control custom-radio">
                  <input
                    type="radio"
                    class="custom-control-input"
                    id="<?php echo "vote_case_{$game_bet_type->id}_{$game_bet_type_index}_win"; ?>"
                    name="<?php echo "votes[{$game_bet_type_index}][vote_case]" ?>"
                    value="<?php echo Vote::$VOTE_CASE_WIN; ?>"
                  >
                  <label
                    class="custom-control-label"
                    for="<?php echo "vote_case_{$game_bet_type->id}_{$game_bet_type_index}_win"; ?>">
                  </label>
                </div>
              </div>
              <div class="game-betting-case">
                <div class="case-title">
                <span class="text-danger">↓</span>&nbsp;<?php echo trans('app.under'); ?>
                </div>
                <div class="case-value custom-control custom-radio">
                  <input
                    type="radio"
                    class="custom-control-input"
                    id="<?php echo "vote_case_{$game_bet_type->id}_{$game_bet_type_index}_loss"; ?>"
                    name="<?php echo "votes[{$game_bet_type_index}][vote_case]" ?>"
                    value="<?php echo Vote::$VOTE_CASE_LOSS; ?>"
                  >
                  <label
                    class="custom-control-label"
                    for="<?php echo "vote_case_{$game_bet_type->id}_{$game_bet_type_index}_loss"; ?>">
                  </label>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php $game_bet_type_index++; endif; endforeach; ?>
    </div>
    <div class="text-right">
      <button type="submit" class="btn btn-success"><?php echo trans('app.vote'); ?></button>
    </div>
  </form>
</div>