
<?php
if (count($leagues_groups) > 0) {
  foreach($leagues_groups as $leagues_group) {
    $league = $leagues_group->league;
    $games = $leagues_group->games; ?>
    <?php require(dirname(__FILE__) . '/league-games.php'); ?>
  <?php }
} else { 
  $alert = (object)[
    'status' => 'warning',
    'message' => trans('app.there_are_no_games_scheduled_for_this_day')
  ]; ?>
  <div class="m-3">
    <?php require(dirname(__FILE__) . '/alert.php'); ?>
  </div>
<?php }
?>
