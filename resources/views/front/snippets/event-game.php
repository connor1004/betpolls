<?php
  use Carbon\Carbon;
  use App\ManualPollPage;
  use App\ManualEventVote;
  use App\Vote;
  use App\Facades\Geoip;
  $votes = [];
  
  $poll = $poll_content['poll'];
  $bet_types = $poll_content['bet_types'];
  $locale = app('translator')->getLocale();
?>
<app-event-game
  class="game-item<?php echo isset($active) ? " active" : ""; ?>"
  id="<?php echo "game_{$poll->candidate1_id}_{$poll->candidate2_id}"; ?>"
>
  <table class="table game-main">
    <tr class="game-display">
      <td class="game-start-time" colspan="5">
        <b><?php echo $poll->locale_name; ?></b>
        <?php if (isset($vote_error)) : ?>
          <span class="vote-error"><?php echo $vote_error; ?></span>
        <?php endif; ?>
      </td>
    </tr>
    <?php
      $candidate = $poll->candidate1;
      $is_first = true;
      $is_winner = $poll->candidate1_score > $poll->candidate2_score ? true: false;
      require(dirname(__FILE__) . '/event-game-candidate.php');
      
      $candidate = $poll->candidate2;
      $is_first = false;
      $is_winner = $poll->candidate1_score < $poll->candidate2_score ? true: false;
      require(dirname(__FILE__) . '/event-game-candidate.php');
    ?>
    <tr class="game-actions">
      <td colspan="5">
        <?php if ($bet_types->count() > 0) : ?>
          <?php if (($poll_page->status !== ManualPollPage::$STATUS_NOT_STARTED)
           || $poll_page->start_at < (new Carbon())->format('Y-m-d H:i:s')) : ?>
            <button type="button" class="btn btn-primary game-show-more"><?php echo trans('app.results'); ?></button>
          <?php else : ?>
            <?php if ($user) :  
              $votes = ManualEventVote::where(['user_id' => $user->id, 'event_id' => $poll->id])->get();
              if (count($votes) > 0) : ?>
                <button type="button" class="btn btn-primary game-show-more"><?php echo trans('app.results'); ?></button>
              <?php else : ?>
                <button type="button" class="btn btn-success game-show-more"><?php echo trans('app.vote'); ?></button>
              <?php endif; ?>
            <?php else : ?>
              <a href="<?php echo url('login').'?redirect=/future/'.$poll_page->id; ?>" class="btn btn-success"><?php echo trans('app.vote'); ?></a>
            <?php endif; ?>
          <?php endif; ?>
        <?php endif; ?>
      </td>
    </tr>
  </table>
  <div class="game-details">
    <?php require(dirname(__FILE__) . '/event-game-bettings.php'); ?>
  </div>
</app-event-game>