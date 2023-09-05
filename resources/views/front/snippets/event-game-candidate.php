<?php
  use App\ManualPollPage;
  use App\BetType;
  use App\Vote;
  use App\Facades\Format;
  $has_over_under = false;

  $class_loser = ' loser';
  if ($poll_page->status == 'ended') {
    $class_loser = !$is_winner? ' loser' : '';
  }
  
  $show_scores = $poll_page->show_scores;
  $standing = $is_first ? $poll->candidate1_standing : $poll->candidate2_standing;
  $score = $is_first ? $poll->candidate1_score : $poll->candidate2_score;
  $odds = $is_first ? $poll->candidate1_odds : $poll->candidate2_odds;
  $vote_case = $is_first ? Vote::$VOTE_CASE_WIN : Vote::$VOTE_CASE_LOSS;
  $spread = $is_first ? $poll->spread : -$poll->spread;
?>
<tr class="game-candidate">
  <td>
    <div class="candidate-main<?php if (count($bet_types) > 0) : echo " candidate-main-has-bets"; endif; ?>">
      <div class="candidate-logo-name">
        <?php if ($candidate->logo_url) : ?>
          <div class="candidate-logo">
            <img src="<?php echo $candidate->logo_url; ?>">
          </div>
        <?php endif; ?>
        <div class="candidate-name<?php echo $class_loser; ?>">
          <span class="d-block d-lg-none">
            <?php echo $candidate->locale_short_name; ?>
          </span>
          <span class="d-none d-lg-block">
            <?php echo $candidate->locale_name; ?>
          </span>
        </div>
      </div>
      <?php if ($standing) : ?>
        <div class="candidate-standing">
          <?php echo '('.$standing.')'; ?>
        </div>
      <?php endif; ?>
    </div>
  </td>
  <td class="candidate-score<?php echo $class_loser; ?>" colspan="<?php echo 4-count($bet_types); ?>">
    <?php if ($poll_page->status === ManualPollPage::$STATUS_STARTED || $poll_page->status === ManualPollPage::$STATUS_ENDED) : ?>
      <?php if ($show_scores) : ?>
        <?php echo $score; ?>
      <?php endif; ?>
    <?php endif; ?>
  </td>
  <?php foreach($bet_types as $event_bet_type) : ?>
    <?php if ($event_bet_type->bet_type->value === BetType::$VALUE_SPREAD) : ?>
      <td class="text-center <?php echo $event_bet_type->match_case === $vote_case ? "text-success": "" ?>">
        <?php echo Format::formatSignedWeight($spread); ?>
      </td>
    <?php elseif ($event_bet_type->bet_type->value === BetType::$VALUE_MONEYLINE) : ?>
      <td class="text-center <?php echo $event_bet_type->match_case === $vote_case ? "text-success": "" ?>">
        <?php echo Format::formatSignedWeight($odds); ?>
      </td>
    <?php else :
      $has_over_under = true;
      if ($is_first) :
    ?>
      <td class="text-center align-middle" rowspan="4">
        <div class="<?php echo $event_bet_type->match_case === Vote::$VOTE_CASE_WIN ? 'game-bet-over' : ($event_bet_type->match_case === Vote::$VOTE_CASE_LOSS ? 'game-bet-under' : '')  ?>">
          <?php echo $poll->over_under; ?>
        </div>
      </td>
    <?php endif; endif; ?>
  <?php endforeach; ?>
</tr>
<tr class="game-candidate-content">
  <td colspan="<?php echo (5 - ($has_over_under ? 1 : 0)); ?>">
    <?php
      $team_meta = $candidate->locale_meta;
    ?>
    <?php require(dirname(__FILE__) . '/event-game-candidate-content.php'); ?>
  </td>
</tr>