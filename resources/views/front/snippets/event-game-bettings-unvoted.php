<?php
  use App\BetType;
  use App\ManualEvent;
  use App\ManualEventBetType;
  use App\Facades\Format;
  use App\Vote;

  $form_action = $locale == 'es' ? url('es/futuros/event', [$poll->id, 'voto']) : url('futures/event', [$poll->id, 'vote']);
?>
<div class="game-bettings game-bettings-unvoted">
  <form action="<?php echo $form_action; ?>" method="post">
    <div class="row">
      <?php $event_bet_types = $poll_content['bet_types'];
        $event_bet_type_index = 0;
        foreach($event_bet_types as $event_bet_type) : 
          $bet_type = $event_bet_type->bet_type;
      ?>
        <div class="col-12 col-md-6 col-xl-4 mb-2">
          <div class="game-betting"> 
            <!-- GAME BET TYPE ID -->
            <input
              type="hidden"
              name="<?php echo "votes[{$event_bet_type_index}][event_bet_type_id]"; ?>"
              value="<?php echo $event_bet_type->id; ?>"
            >

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
                <div class="case-value custom-control custom-radio">
                  <input
                    type="radio"
                    class="custom-control-input"
                    id="<?php echo "vote_case_{$event_bet_type->id}_{$event_bet_type_index}_win"; ?>"
                    name="<?php echo "votes[{$event_bet_type_index}][vote_case]" ?>"
                    value="<?php echo Vote::$VOTE_CASE_WIN; ?>"
                  >
                  <label
                    class="custom-control-label"
                    for="<?php echo "vote_case_{$event_bet_type->id}_{$event_bet_type_index}_win"; ?>">
                  </label>
                </div>
              </div>
              <div class="game-betting-case">
                <div class="case-title">
                  <?php echo $poll->candidate2->locale_short_name; ?>
                  <?php echo Format::formatSignedWeight(-$poll->spread); ?>
                </div>
                <div class="case-value custom-control custom-radio">
                  <input
                    type="radio"
                    class="custom-control-input"
                    id="<?php echo "vote_case_{$event_bet_type->id}_{$event_bet_type_index}_loss"; ?>"
                    name="<?php echo "votes[{$event_bet_type_index}][vote_case]" ?>"
                    value="<?php echo Vote::$VOTE_CASE_LOSS; ?>"
                  >
                  <label
                    class="custom-control-label"
                    for="<?php echo "vote_case_{$event_bet_type->id}_{$event_bet_type_index}_loss"; ?>">
                  </label>
                </div>
              </div>
            
            <!-- MONEY LINE -->
            <?php elseif ($bet_type->value === BetType::$VALUE_MONEYLINE) : ?>
              <div class="game-betting-case">
                <div class="case-title">
                  <?php echo $poll->candidate1->locale_short_name; ?>
                  <?php echo Format::formatSignedWeight($poll->candidate1_odds); ?>
                </div>
                <div class="case-value custom-control custom-radio">
                  <input
                    type="radio"
                    class="custom-control-input"
                    id="<?php echo "vote_case_{$event_bet_type->id}_{$event_bet_type_index}_win"; ?>"
                    name="<?php echo "votes[{$event_bet_type_index}][vote_case]" ?>"
                    value="<?php echo Vote::$VOTE_CASE_WIN; ?>"
                  >
                  <label
                    class="custom-control-label"
                    for="<?php echo "vote_case_{$event_bet_type->id}_{$event_bet_type_index}_win"; ?>">
                  </label>
                </div>
                <div class="favicon">
                  <Image src="<?php echo URL::asset('/media/favicon-32x32.png'); ?>" height="16" width="16" />
                </div>
                <div class="case-title ml-2">
                  <?php echo Format::formatSignedWeight($poll->moneyline1_win_points); ?>
                   | 
                  <?php echo Format::formatSignedWeight($poll->moneyline1_loss_points); ?>
                </div>
              </div>
              <div class="game-betting-case">
                <div class="case-title">
                  <?php echo $poll->candidate2->locale_short_name; ?>
                  <?php echo Format::formatSignedWeight($poll->candidate2_odds); ?>
                </div>
                <div class="case-value custom-control custom-radio">
                  <input
                    type="radio"
                    class="custom-control-input"
                    id="<?php echo "vote_case_{$event_bet_type->id}_{$event_bet_type_index}_loss"; ?>"
                    name="<?php echo "votes[{$event_bet_type_index}][vote_case]" ?>"
                    value="<?php echo Vote::$VOTE_CASE_LOSS; ?>"
                  >
                  <label
                    class="custom-control-label"
                    for="<?php echo "vote_case_{$event_bet_type->id}_{$event_bet_type_index}_loss"; ?>">
                  </label>
                </div>
                <div class="favicon">
                  <Image src="<?php echo URL::asset('/media/favicon-32x32.png'); ?>" height="16" width="16" />
                </div>
                <div class="case-title ml-2">
                  <?php echo Format::formatSignedWeight($poll->moneyline2_win_points); ?>
                   | 
                  <?php echo Format::formatSignedWeight($poll->moneyline2_loss_points); ?>
                </div>
              </div>

              <?php if (!empty($poll->tie_odds)) : ?>
                <div class="game-betting-case">
                  <div class="case-title">
                    <?php echo trans('app.tie') ?>
                    <?php echo Format::formatSignedWeight($poll->tie_odds); ?>
                  </div>
                  <div class="case-value custom-control custom-radio">
                    <input
                      type="radio"
                      class="custom-control-input"
                      id="<?php echo "vote_case_{$event_bet_type->id}_{$event_bet_type_index}_tie"; ?>"
                      name="<?php echo "votes[{$event_bet_type_index}][vote_case]" ?>"
                      value="<?php echo Vote::$VOTE_CASE_TIE; ?>"
                    >
                    <label
                      class="custom-control-label"
                      for="<?php echo "vote_case_{$event_bet_type->id}_{$event_bet_type_index}_tie"; ?>">
                    </label>
                  </div>
                  <div class="favicon">
                    <img src="<?php echo URL::asset('/media/favicon-32x32.png'); ?>" height="16" width="16" />
                  </div>
                  <div class="case-title ml-2">
                    <?php echo Format::formatSignedWeight($poll->moneyline_tie_win_points); ?>
                    | 
                    <?php echo Format::formatSignedWeight($poll->moneyline_tie_loss_points); ?>
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
                    id="<?php echo "vote_case_{$event_bet_type->id}_{$event_bet_type_index}_win"; ?>"
                    name="<?php echo "votes[{$event_bet_type_index}][vote_case]" ?>"
                    value="<?php echo Vote::$VOTE_CASE_WIN; ?>"
                  >
                  <label
                    class="custom-control-label"
                    for="<?php echo "vote_case_{$event_bet_type->id}_{$event_bet_type_index}_win"; ?>">
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
                    id="<?php echo "vote_case_{$event_bet_type->id}_{$event_bet_type_index}_loss"; ?>"
                    name="<?php echo "votes[{$event_bet_type_index}][vote_case]" ?>"
                    value="<?php echo Vote::$VOTE_CASE_LOSS; ?>"
                  >
                  <label
                    class="custom-control-label"
                    for="<?php echo "vote_case_{$event_bet_type->id}_{$event_bet_type_index}_loss"; ?>">
                  </label>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php $event_bet_type_index++; endforeach; ?>
    </div>
    <div class="text-right">
      <button type="submit" class="btn btn-success"><?php echo trans('app.vote'); ?></button>
    </div>
  </form>
</div>