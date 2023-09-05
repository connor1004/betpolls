<?php
  use Carbon\Carbon;
  use App\ManualPollPage;
  use App\ManualFutureVote;
  use App\Vote;
  use App\Facades\Geoip;
  $poll = $poll_content['poll'];
  $answers = $poll_content['answers'];
  $votes = [];
  if ($user) {
    $votes = ManualFutureVote::where(['user_id' => $user->id, 'future_id' => $poll->id])->get();
  }
  $locale = app('translator')->getLocale();
  $form_action = $locale == 'es' ? url('es/futuros/multi', [$poll->id, 'voto']) : url('futures/multi', [$poll->id, 'vote']);
  $show_odds = false;
  $show_points = false;
  $show_absent = false;
  foreach ($answers as $answer) {
    if ($answer->odds) {
      $show_odds = true;
    }
    if ($answer->winning_points || $answer->losing_points) {
      $show_points = true;
    }
    if ($answer->is_absent) {
      $show_absent = true;
    }
  }
?>
<app-future-game
  class="game-item<?php echo isset($active) ? " active" : ""; ?>"
  id="<?php echo "future_game_{$poll->id}"; ?>"
>
  <form action="<?php echo $form_action; ?>" method="post">
    <table class="table game-main">
      <tr class="game-display">
        <td class="game-start-time" colspan="6">
          <b><?php echo $poll->locale_name; ?></b>
          <?php if (isset($vote_error)) : ?>
            <span class="vote-error"><?php echo $vote_error; ?></span>
          <?php endif; ?>
        </td>
      </tr>
      <?php
        foreach ($answers as $answer) {
          require(dirname(__FILE__) . '/future-game-candidate.php');
        }
      ?>
      <tr class="game-actions">
        <td colspan="6">
          <?php if (($poll_page->status !== ManualPollPage::$STATUS_NOT_STARTED)
            || $poll_page->start_at < (new Carbon())->format('Y-m-d H:i:s')) : ?>
            <button type="button" class="btn btn-primary game-show-more"><?php echo trans('app.results'); ?></button>
          <?php else : ?>
            <?php if ($user) :
              if (count($votes) > 0) : ?>
                <button type="button" class="btn btn-primary game-show-more"><?php echo trans('app.results'); ?></button>
              <?php else : ?>
                <button type="button" class="btn btn-success game-show-more"><?php echo trans('app.vote'); ?></button>
              <?php endif; ?>
            <?php else : ?>
              <a 
                href="<?php echo url('login').'?redirect=/'.($locale == 'es' ? 'es/futuro' : 'future').'/'.$poll_page->id; ?>"
                class="btn btn-success"
              ><?php echo trans('app.vote'); ?></a>
            <?php endif; ?>
          <?php endif; ?>
        </td>
      </tr>
      <?php if (($poll_page->status == ManualPollPage::$STATUS_NOT_STARTED)
        && $poll_page->start_at > (new Carbon())->format('Y-m-d H:i:s')
        && count($votes) < 1) : ?>
        <tr class="game-submit">
          <td colspan="6">
            <button type="submit" class="btn btn-success"><?php echo trans('app.vote'); ?></button>
          </td>
        </tr>
      <?php endif; ?>
    </table>
  </form>
</app-future-game>