<?php
  use Carbon\Carbon;
  use App\ManualPollPage;
  use App\BetType;
  use App\Vote;
  use App\Facades\Format;
  $has_over_under = false;

  $class_loser = ' loser';
  $class_success = '';
  if ($poll_page->status == 'ended') {
    $class_loser = $answer->score < 1 ? ' loser' : '';
    $class_success = $answer->score > 0 ? ' text-success' : '';
  }
  
  $show_scores = $poll_page->show_scores;
  $candidate = $answer->candidate;
?>
<tr class="game-candidate future<?php echo $class_loser; ?>">
  <td>
    <div class="candidate-main candidate-main-has-bets">
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
      <?php if ($answer->standing) : ?>
        <div class="candidate-standing">
          <?php echo '('.$answer->standing.')'; ?>
        </div>
      <?php endif; ?>
    </div>
  </td>
  <td class="candidate-score<?php echo $class_loser; ?>">
    <?php if ($poll_page->status === ManualPollPage::$STATUS_STARTED || $poll_page->status === ManualPollPage::$STATUS_ENDED) : ?>
      <?php if ($show_scores) : ?>
        <?php echo $answer->score; ?>
      <?php endif; ?>
    <?php endif; ?>
  </td>
  <td class="candidate-progress">
    <div class="only-active">
      <?php if (count($votes) > 0) {
        $percent = 0;
        if ($poll->voter_count > 0 && $answer->vote_count > 0) {
          $percent = round(($answer->vote_count / $poll->voter_count) * 100, 2);
        }
        $game_voted_progress = (object)[
          'percent' => $percent,
          'count' => $answer->vote_count,
        ];
        require(dirname(__FILE__) . '/game-voted-progress.php');
      } else if (($poll_page->status === ManualPollPage::$STATUS_NOT_STARTED)
        && $poll_page->start_at > (new Carbon())->format('Y-m-d H:i:s')) {?>
        <div class="case-value custom-control custom-radio">
          <input
            type="radio"
            class="custom-control-input"
            id="<?php echo "vote_candidate_{$poll->id}_{$answer->id}"; ?>"
            name="<?php echo "voted_answer" ?>"
            value="<?php echo $answer->id; ?>"
          >
          <label
            class="custom-control-label"
            for="<?php echo "vote_candidate_{$poll->id}_{$answer->id}"; ?>">
          </label>
        </div>
      <?php } ?>
    </div>
  </td>
  <?php if ($show_odds) : ?>
    <td class="text-center<?php echo $class_success; ?>">
      <?php if ($answer->odds) : ?>
        <?php echo Format::formatSignedWeight($answer->odds); ?>
      <?php endif; ?>
    </td>
  <?php endif; ?>
  <?php if ($show_points) : ?>
    <td class="candidate-points <?php echo $class_success; ?>">
      <img src="<?php echo URL::asset('/media/favicon-32x32.png'); ?>" height="16" width="16" />
      <?php if ($answer->winning_points || $answer->losing_points) : ?>
        <?php echo Format::formatSignedWeight($answer->winning_points); ?> | <?php echo Format::formatSignedWeight($answer->losing_points); ?>
      <?php endif; ?>
    </td>
  <?php endif; ?>
  <?php if ($show_absent) : ?>
    <td class="text-center">
      <?php echo $answer->is_absent ? '◎' : '' ?>
    </td>
  <?php endif; ?>
</tr>
<tr class="game-candidate-content">
  <td colspan="6">
    <?php
      $team_meta = $candidate->locale_meta;
    ?>
    <?php require(dirname(__FILE__) . '/future-game-candidate-content.php'); ?>
  </td>
</tr>