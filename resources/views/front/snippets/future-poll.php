<?php
  use App\Facades\Data;
  use App\Facades\Geoip;
  use Carbon\Carbon;
  
  if ($poll_page->is_future == 1) {
    $start_at = Geoip::getLocalizedDate($poll_page->start_at, 'date');
  } else {
    $start_at = Geoip::getLocalizedDate($poll_page->start_at, 'date_time');
  }
  
  $user = Data::getUser();
  $locale = app('translator')->getLocale();
?>
<div class="card future-poll-card">
  <div class="card-header">
    <div class="d-flex align-items-center">
      <?php if ($poll_page->logo) : ?>
        <div class="future-poll-logo">
          <img src="<?php echo $poll_page->logo ?>">
        </div>
      <?php endif; ?>
      <div class="future-poll-name">
        <strong><?php echo $poll_page->locale_name; ?></strong>
      </div>
    </div>
  </div>
  <div class="future-polls card-body">
    <div class="row future-poll-content">
      <?php if ($poll_page->locale_location) : ?>
        <div class="col-xs-12 col-sm-7">
          <b><?php echo trans('app.location'); ?>:</b> <?php echo $poll_page->locale_location; ?>
        </div>
      <?php endif; ?>
      <div class="col-xs-12 col-sm-5">
        <b><?php echo trans('app.date'); ?>:</b> <?php echo $start_at; ?>
      </div>
    </div>
    <?php if (count($polls) > 0) : $i = 0; ?>
      <?php foreach ($polls as $poll_content) : ?>
        <?php if ($poll_page->is_future == 1) : ?>
          <?php require(dirname(__FILE__) . '/future-game.php'); ?>
        <?php else : ?>
          <?php require(dirname(__FILE__) . '/event-game.php'); ?>
        <?php endif; ?>
        <?php $i++; ?>
        <?php if ($i % 4 == 0) : ?>
        <app-game class="game-item">
          <div>
            <?php 
              $banner_settings = (object)[
                'banner_name' => 'game',
                'style' => 'display: inline-block; width: 100%; height: 90px;',
              ];
              require(dirname(__FILE__) . '/../widgets/banner.php');
            ?>
          </div>
        </app-game>
      <?php endif; ?>
      <?php endforeach ?>
      <?php if (count($polls) < 4) : ?>
        <app-game class="game-item">
          <div>
            <?php 
              $banner_settings = (object)[
                'banner_name' => 'game',
                'style' => 'display: inline-block; width: 100%; height: 90px;',
              ];
              require(dirname(__FILE__) . '/../widgets/banner.php');
            ?>
          </div>
        </app-game>
      <?php endif; ?>
    <?php else :
      $alert = (object)[
        'status' => 'warning',
        'message' => trans('app.there_are_no_polls')
      ];
    ?>
      <div class="m-3">
        <?php require(dirname(__FILE__) . '/alert.php'); ?>
      </div>
    <?php endif ?>
  </div>
</div>
