<?php
  use Illuminate\Support\Facades\Request;
  use App\Leaderboard;
  use App\User;
?>
<table class="table table-bordered table-hover table-striped">
  <thead>
    <th><?php echo trans('app.rank'); ?></th>
    <th class="w-100"><?php echo trans('app.username'); ?></th>
    <th><?php echo trans('app.pts'); ?></th>
  </thead>
  <tbody>
    <?php if($leaderboards->count() === 0) : ?>
      <td colspan="3" class="text-center">
        <?php echo trans('app.no_results_found'); ?>
      </td>
    <?php else : ?>
      <?php $rank = $leaderboards->firstItem(); ?>
      <?php foreach($leaderboards as $leaderboard) : 
        $user = User::find($leaderboard->user_id);
        $latest_leaderboard = $user->getLatestLeaderboard($type, $sport_category_id, $league_id, $static_period_type);
        // $latest_point = $user->getLatestPoint($type, $sport_category_id, $league_id);
        $recent_point = $user->getRecentPoint($type, $sport_category_id, $league_id);
      ?>
        <tr>
          <td><?php echo $rank; ?></td>
          <td>
            <?php $country = strtolower($user->country); ?>
            <span class="flag-icon <?php echo "flag-icon-{$country}"; ?>"></span>
            <a href="<?php echo $user->locale_url; ?>"><?php echo $user->username; ?></a>
            <?php if ($recent_point) : ?>
              (<?php echo $recent_point->score; ?>)
              <?php if ($recent_point->position >= 1 && $recent_point->position <= 3) : ?>
                <i class="image-icon image-icon-sm image-icon-trophy-<?php echo $recent_point->position; ?>"></i>
              <?php endif; ?>
            <?php endif; ?>
            <?php if ($latest_leaderboard) : ?>
              <?php if ($latest_leaderboard->position >= 1 && $latest_leaderboard->position <= 3) : ?>
                <i class="image-icon image-icon-sm image-icon-medal-weekly-<?php echo $latest_leaderboard->position; ?>"></i>
              <?php endif; ?>
            <?php endif; ?>
          </td>
          <td><?php echo $leaderboard->score; ?></td>
        </tr>
      <?php $rank++; endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
<?php if ($leaderboards->lastPage() >= 1): ?>
<?php echo $leaderboards->appends(Request::query())->links('pagination::simple-bootstrap-4'); ?>
<?php endif; ?>