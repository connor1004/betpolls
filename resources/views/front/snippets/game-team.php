<?php
  use App\Game;
  use App\BetType;
  use App\Vote;
  use App\Facades\Format;
  $has_over_under = false;
  $class_loser = !$is_winner && $show_scores ? ' loser' : '';
?>
<tr class="game-team">
  <td>
    <div class="team-main<?php if (count($available_game_bet_types) > 0) : echo " team-main-has-bets"; endif; ?>">
      <div class="team-logo-name">
        <div class="team-logo">
          <img src="<?php echo $game_team->team->logo; ?>">
        </div>
        <div class="team-name<?php echo $class_loser; ?>">
          <span class="d-block d-lg-none">
            <?php echo $game_team->team->locale_short_name; ?>
          </span>
          <span class="d-none d-lg-block">
            <?php echo $game_team->team->locale_name; ?>
          </span>
        </div>
      </div>
      <?php if (!$league->hide_standings) : ?>
        <div class="team-standing">
          <?php
            if (!empty($game_team->general_info)
              && isset($game_team->general_info['standings'])
              && isset($game_team->general_info['standings']['w'])
              && isset($game_team->general_info['standings']['l'])
            ) {
              if (isset($game_team->general_info['standings']['d'])) {
                printf("(%s-%s-%s)", $game_team->general_info['standings']['w'], $game_team->general_info['standings']['l'], $game_team->general_info['standings']['d']);
              } else if (isset($game_team->general_info['standings']['otl'])) {
                printf("(%s-%s-%s)", $game_team->general_info['standings']['w'], $game_team->general_info['standings']['l'], $game_team->general_info['standings']['otl']);
              } else {
                printf("(%s-%s)", $game_team->general_info['standings']['w'], $game_team->general_info['standings']['l']);
              }
            }
          ?>
        </div>
          <?php endif; ?>
    </div>
  </td>
  <td class="team-score<?php echo $class_loser; ?>" colspan="<?php echo 4-count($available_game_bet_types); ?>">
    <?php if ($game_status === Game::$STATUS_STARTED || $game_status === Game::$STATUS_ENDED) : ?>
      <?php if (!$show_scores) : ?>
        <?php echo $game_team->score; ?>
      <?php else : ?>
        <?php foreach ($game_team->scores as $score) : ?>
          <div class="team-score-element"><?php echo $score; ?></div>
        <?php endforeach; ?>
      <?php endif; ?>
    <?php endif; ?>
  </td>
  <?php foreach($available_game_bet_types as $available_game_bet_type) : ?>
    <?php if (!$game->is_nulled && $available_game_bet_type->bet_type->value === BetType::$VALUE_SPREAD) : ?>
      <td class="text-center <?php echo $available_game_bet_type->match_case === $game_team->vote_case ? "text-success": "" ?>">
        <?php echo Format::formatSignedWeight($available_game_bet_type[$game_team->weight]); ?>
      </td>
    <?php elseif ($available_game_bet_type->bet_type->value === BetType::$VALUE_MONEYLINE) : ?>
      <td class="text-center <?php echo $available_game_bet_type->match_case === $game_team->vote_case ? "text-success": "" ?>">
        <?php echo Format::formatSignedWeight($available_game_bet_type[$game_team->weight]); ?>
      </td>
    <?php elseif (!$game->is_nulled) :
      $has_over_under = true;
      if ($is_first) :
    ?>
      <td class="text-center align-middle" rowspan="4">
        <div class="<?php echo $available_game_bet_type->match_case === Vote::$VOTE_CASE_WIN ? 'game-bet-over' : ($available_game_bet_type->match_case === Vote::$VOTE_CASE_LOSS ? 'game-bet-under' : '')  ?>">
          <?php echo $available_game_bet_type->weight_1; ?>
        </div>
      </td>
    <?php endif; endif; ?>
  <?php endforeach; ?>
</tr>
<tr class="game-team-content">
  <td colspan="<?php echo (5 - ($has_over_under ? 1 : 0)); ?>">
    <?php
      $team_meta = $game_team->team->locale_meta;
      $game_team_meta = isset($game->locale_meta[$game_team->key]) ? $game->locale_meta[$game_team->key] : $game->locale_meta[$game_team->key];
    ?>
    <?php require(dirname(__FILE__) . '/game-team-content.php'); ?>
  </td>
</tr>