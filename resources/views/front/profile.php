<?php 
use Illuminate\Support\Facades\Auth;
use App\Facades\Constants;
?>
<?php require_once(dirname(__FILE__) . '/partials/header.php'); ?>
<?php
  $user = Auth::user();
  $medals_groups = $user->medals_groups;
  $trophies = $user->trophies;
  $recent_point = $user->getRecentPoint(0, 0, 0);
?>
<div class="container">
  <div class="row">
    <div class="col-12 col-xl-8 col-lg-7">
      <div class="card">
        <div class="card-header">
          <?php echo trans('app.user_info'); ?>
        </div>
        <div class="card-body">
          <div>
            <label class="h-label"><?php echo trans('app.name'); ?>: </label>
            <label><?php echo $user->name; ?></label>
          </div>
          <div>
            <label class="h-label"><?php echo trans('app.username'); ?>: </label>
            <label><?php echo $user->username; ?></label>
          </div>
          <div>
            <label class="h-label"><?php echo trans('app.email'); ?>: </label>
            <label><?php echo $user->email; ?></label>
          </div>
          <div>
            <label class="h-label"><?php echo trans('app.country'); ?>: </label>
            <?php $countries = Constants::getCountries(); ?>
            <?php foreach($countries as $key => $value) : ?>
              <?php if ($key === $user->country) : ?>
                <label><?php echo $value ?></label>
                <?php $country = strtolower($user->country); ?>
                &nbsp;<span class="flag-icon <?php echo "flag-icon-{$country}"; ?>"></span>
              <?php endif; endforeach; ?>
          </div>
        </div>
      </div>
      <?php if (count($medals_groups) !== 0 || count($trophies) !== 0) : ?>
        <div class="card">
          <div class="card-header">
            <?php echo trans('app.my_rewards'); ?>
          </div>
          <div class="card-body">
            <?php
              foreach ($medals_groups as $medals_group_key => $medals_group):
                if (count($medals_group->items) > 0) : ?>
                  <div>
                    <label class="h-label-md"><?php echo $medals_group->label; ?>:</label>
                    <?php foreach ($medals_group->items as $medal) : ?>
                      <span class="mx-2">
                        <i class="<?php echo "image-icon image-icon-medal-$medals_group_key-$medal->position"; ?>"></i>
                        <?php echo $medal->count; ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                <?php endif;
              endforeach;
            ?>
            <?php if ($trophies && count($trophies) > 0) : ?>
              <div>
                <label class="h-label-md"><?php echo trans('app.trophies'); ?>:</label>
                <?php foreach ($trophies as $trophy) : ?>
                  <span class="mx-2">
                    <i class="image-icon image-icon-trophy-<?php echo $trophy->position; ?>"></i>
                    <?php echo $trophy->count; ?>
                  </span>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
            <!-- <?php if ($recent_point) : ?>
              <div>
                <label class="h-label-md"><?php echo trans("app.ranking"); ?>:</label>
                <span class="mx-2">
                  <?php if ($recent_point->position >= 1 && $recent_point->position <=3) : ?>
                  <i class="image-icon image-icon-trophy-<?php echo $recent_point->position; ?>"></i>
                  <?php else : ?>
                    <?php echo $recent_point->position; ?>
                  <?php endif; ?>
                </span>
              </div>
            <?php endif; ?> -->
          </div>
        </div>
      <?php endif; ?>
      <?php require(dirname(__FILE__) . '/snippets/user-voted-games.php'); ?>
    </div>
    <div class="col-12 col-xl-4 col-lg-5">
      <?php require(dirname(__FILE__) . '/partials/sidebar-secondary.php'); ?>
    </div>
  </div>
</div>
<?php require_once(dirname(__FILE__) . '/partials/footer.php'); ?>