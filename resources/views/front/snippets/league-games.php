<?php
  use App\Facades\Data;
  $user = Data::getUser();
?>
<app-league-games class="card league-games-card">
  <div class="card-header">
    <div class="d-flex align-items-center">
      <a class="league-logo" href="<?php echo $league->locale_url; ?>">
        <img src="<?php echo $league->logo ?>">
      </a>
      <a class="league-name" href="<?php echo $league->locale_url; ?>">
        <strong><?php echo $league->locale_name; ?></strong>
      </a>
    </div>
  </div>
  <div class="league-games card-body">
    <?php if (count($games) > 0) : $i = 0; ?>
      <?php foreach ($games as $game) : ?>
        <?php if ($game->home_team && $game->away_team) : ?>
          <?php require(dirname(__FILE__) . '/game.php'); ?>
        <?php endif; ?>
        <?php $i++; ?>
        <?php if ($i % 3 == 0) : ?>
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
      <?php if (count($games) < 3) : ?>
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
        'message' => trans('app.there_are_no_games_scheduled_for_this_day')
      ];
    ?>
      <div class="m-3">
        <?php require(dirname(__FILE__) . '/alert.php'); ?>
      </div>
    <?php endif ?>
  </div>
</app-league-games>
