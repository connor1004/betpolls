<?php 
  use App\Facades\Constants;
  use App\Facades\Utils;
  use App\Leaderboard;
  use App\User;

?>
<div class="card widget leaderboard-widget">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <a href="<?php echo Utils::localeUrl('leaderboard'); ?>">
          <b><?php echo $leaderboard_settings->title; ?></b>
        </a>
      </div>
      <form action="<?php echo Utils::localeUrl('leaderboard'); ?>" style="width: 160px;">
        <?php $period_types = Constants::getSecondPeriodTypes(); ?>
        <select class="select2 form-control" name="period_type" onchange="this.form.submit();">
          <?php foreach($period_types as $key => $value) : ?>
            <option value="<?php echo $key; ?>">
              <?php echo $value; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>
  </div>
  <div class="card-body">
    <table class="table table-bordered table-hover table-striped">
      <thead>
        <th><?php echo trans('app.rank'); ?></th>
        <th class="w-100"><?php echo trans('app.username'); ?></th>
        <th><?php echo trans('app.pts'); ?></th>
      </thead>
      <tbody>
        <?php if(sizeof($leaderboard_settings->leaderboards) == 0) : ?>
          <td colspan="3" class="text-center">
            <?php echo trans('app.no_results_found'); ?>
          </td>
        <?php else : ?>
          <?php $rank = 1; ?>
          <?php foreach($leaderboard_settings->leaderboards as $leaderboard) : 
            $user = User::find($leaderboard->user_id);
          ?>
            <tr>
              <td><?php echo $rank; ?></td>
              <td>
                <?php $country = strtolower($user->country); ?>
                <span class="flag-icon <?php echo "flag-icon-{$country}"; ?>"></span>
                <a href="<?php echo $user->locale_url; ?>"><?php echo $user->username; ?></a>
              </td>
              <td><?php echo $leaderboard->score; ?></td>
            </tr>
          <?php $rank++; endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>