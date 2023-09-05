<?php
  $progress_bar_class = "";
  if ($game_voted_progress->percent < 45) {
    $progress_bar_class = "bg-danger";
  } else if($game_voted_progress->percent < 55) {
    $progress_bar_class = "bg-warning";
  } else {
    $progress_bar_class = "bg-success";
  }
  $game_voted_progress_width = $game_voted_progress->percent * 0.5;
?>
<div class="value-progress">
  <span
    class="progress-bar <?php echo $progress_bar_class; ?>"
    style="width: <?php echo "{$game_voted_progress_width}px" ?>">
  </span>
  <span class="progress-value">
    <?php echo round(abs($game_voted_progress->percent), 2); ?>%
  </span>
  <span class="count">
    (<?php echo $game_voted_progress->count; ?>)
  </span>
</div>