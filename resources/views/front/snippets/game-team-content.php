<?php if (isset($game_team_meta['game_detail']) && !empty($game_team_meta['game_detail'])) : ?>
  <div class="team-content-item">
    <span class="image-icon image-icon-game-detail"></span>
    <div>
      <?php echo $game_team_meta['game_detail'] ?>
    </div>
  </div>
<?php endif; ?>
<?php if (isset($game_team_meta['in']) && !empty($game_team_meta['in'])) : ?>
  <div class="team-content-item">
    <span class="fa-stack fa-sm">
      <i class="fa fa-circle fa-stack-2x text-success"></i>
      <i class="fa fa-sign-in text-whte fa-stack-1x fa-inverse"></i>
    </span>
    <div>
      <?php echo $game_team_meta['in'] ?>
    </div>
  </div>
<?php endif; ?>
<?php if (isset($game_team_meta['questionable']) && !empty($game_team_meta['questionable'])) : ?>
  <div class="team-content-item">
    <span class="fa-stack fa-sm">
      <i class="fa fa-circle fa-stack-2x text-warning"></i>
      <i class="fa fa-question text-dark fa-stack-1x fa-inverse"></i>
    </span>
    <div>
      <?php echo $game_team_meta['questionable'] ?>
    </div>
  </div>
<?php endif; ?>
<?php if (isset($game_team_meta['out']) && !empty($game_team_meta['out'])) : ?>
  <div class="team-content-item">
    <span class="fa-stack fa-sm">
      <i class="fa fa-circle fa-stack-2x text-danger"></i>
      <i class="fa fa-sign-out text-whte fa-stack-1x fa-inverse"></i>
    </span>
    <div>
      <?php echo $game_team_meta['out'] ?>
    </div>
  </div>
<?php endif; ?>
<?php if (isset($team_meta['injured']) && !empty($team_meta['injured'])) : ?>
  <div class="team-content-item">
    <span class="fa-stack fa-sm">
      <i class="fa fa-circle fa-stack-2x text-danger"></i>
      <i class="fa fa-plus text-whte fa-stack-1x fa-inverse"></i>
    </span>
    <div>
      <?php echo $team_meta['injured'] ?>
    </div>
  </div>
<?php endif; ?>
<?php if (isset($game_team_meta['betting_tips']) && !empty($game_team_meta['betting_tips'])) : ?>
  <div class="team-content-item">
    <span class="image-icon image-icon-betting-tips"></span>
    <div>
      <?php echo str_replace("\n", "<br/>", $game_team_meta['betting_tips']); ?>
    </div>
  </div>
<?php endif; ?>