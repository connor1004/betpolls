<?php 
  use App\Vote;
  use App\BetType;
  use Carbon\Carbon;
  use App\Facades\Format;
  use App\Facades\Geoip;
  use Illuminate\Support\Facades\Request;
  $medals_groups = $voter->medals_groups;
  $trophies = $voter->trophies;
  $recent_point = $voter->getRecentPoint(0, 0, 0);

  function matchPercentageBadge($leaderboard) {
    $percentage = 0;
    if ($leaderboard->calculated_vote_count > 0) {
      $percentage = round($leaderboard->matched_vote_count / $leaderboard->calculated_vote_count * 100, 2);
    }

    if ($percentage < 45) {
        $className = 'badge-danger';
    } elseif ($percentage < 55) {
        $className = 'badge-warning';
    } else {
        $className = 'badge-success';
    }

    return "<span class=\"badge {$className}\">{$percentage}%</span>";
  }
?>
<div class="card user-voted-games-card">
  <div class="card-header bg-dark text-white">
    <div class="d-flex justify-content-between flex-wrap">
      <div>
        <span class="ml-1 flag-icon flag-icon-<?php echo strtolower($voter->country); ?>"></span>
        <b>
          <?php echo $voter->username; ?>
          <?php if ($recent_point) echo "({$recent_point->score})"; ?>
        </b>
      </div>
      <?php if ($weekly_leaderboard || $monthly_leaderboard || $yearly_leaderboard) : ?>
        <div class="d-flex align-items-center">
          <?php if ($yearly_leaderboard) : ?>
            <div class="d-flex flex-column flex-sm-row align-items-center mr-2">
              <b class="mr-1">Y: <?php echo $yearly_leaderboard->score; ?> </b>
              <?php echo matchPercentageBadge($yearly_leaderboard); ?>
            </div>
          <?php endif; ?>
          <?php if ($monthly_leaderboard) : ?>
            <div class="d-flex flex-column flex-sm-row align-items-center mr-2">
              <b class="mr-1">M: <?php echo $monthly_leaderboard->score; ?> </b>
              <?php echo matchPercentageBadge($monthly_leaderboard); ?>
            </div>
          <?php endif; ?>
          <?php if ($weekly_leaderboard) : ?>
            <div class="d-flex flex-column flex-sm-row align-items-center">
              <b class="mr-1">W: <?php echo $weekly_leaderboard->score; ?></b>
              <?php echo matchPercentageBadge($weekly_leaderboard); ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="card-body">
    <div class="border p-2">
      <?php foreach ($medals_groups as $medals_group_key => $medals_group): ?>
        <?php if (count($medals_group->items) > 0) : ?>
          <div class="d-flex">
            <label class="h-label"><?php echo trans("app.{$medals_group_key}"); ?>:</label>
            <?php foreach ($medals_group->items as $medal) : ?>
              <span class="mx-2">
                <i class="<?php echo "image-icon image-icon-medal-$medals_group_key-$medal->position"; ?>"></i>
                <?php echo $medal->count; ?>
              </span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
      <?php if ($trophies) : ?>
        <?php foreach ($trophies as $trophy) : ?>
          <div class="d-flex">
            <label class="h-label"><?php echo trans("app.trophies"); ?>:</label>
            <span class="mx-2">
              <i class="image-icon image-icon-trophy-<?php echo $trophy->position; ?>"></i>
              <?php echo $trophy->position; ?>
            </span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
      <!-- <?php if ($recent_point) : ?>
        <div class="d-flex">
          <label class="h-label"><?php echo trans("app.ranking"); ?>:</label>
          <span class="mx-2">
            <?php if ($recent_point->position >= 1 && $recent_point->position <=3) : ?>
            <i class="image-icon image-icon-trophy-<?php echo $recent_point->position; ?>"></i>
            <?php else : ?>
              <?php echo $recent_point->position; ?>
            <?php endif; ?>
          </span>
        </div>
      <?php endif; ?> -->
      <div class="p-2">
        <a id="games" class="tabLink <?php echo $tab == 'games' ? 'active' : '' ?>">Games</a>
        <a id="events" class="tabLink <?php echo $tab == 'events' ? 'active' : '' ?>">Events</a>
        <a id="futures" class="tabLink <?php echo $tab == 'futures' ? 'active' : '' ?>">Futures</a>
      </div>
    </div>

    <div class="games tabPanel <?php echo $tab == 'games' ? 'active' : '' ?>">
      <table class="table table-striped table-bordered">
        <?php
          $i = 0;
          foreach($games as $game) :
          $home_team_first = $game->between_players || $game->league->sport_category->home_team_first === 1;
          $home_scores = '';
          $away_scores = '';
          if ($game->between_players && $game->game_info) {
            if ($game->game_info['hometeam'] && $game->game_info['hometeam']['score']) {
              foreach ($game->game_info['hometeam']['score'] as $score_item) {
                $score_elements = explode('.', $score_item);
                $element_score = $score_elements[0];
                // if (count($score_elements) > 1) {
                //   $element_score .= '('.$score_elements[1].')';
                // }
                $home_scores .= $element_score.'-';
              }
            }
            if (strlen($home_scores) > 0) {
              $home_scores = substr($home_scores, 0, strlen($home_scores) - 1);
            }
            if ($game->game_info['awayteam'] && $game->game_info['awayteam']['score']) {
              foreach ($game->game_info['awayteam']['score'] as $score_item) {
                $score_elements = explode('.', $score_item);
                $element_score = $score_elements[0];
                // if (count($score_elements) > 1) {
                //   $element_score .= '('.$score_elements[1].')';
                // }
                $away_scores .= $element_score.'-';
              }
            }
            if (strlen($away_scores) > 0) {
              $away_scores = substr($away_scores, 0, strlen($away_scores) - 1);
            }
          }
          if ($home_team_first) {
            $first = (object)[
              'team' => $game->home_team,
              'score' => $game->home_team_score,
              'scores' => $home_scores,
              'weight' => 'weight_1',
              'vote_case' => Vote::$VOTE_CASE_WIN
            ];
            $second = (object)[
              'team' => $game->away_team,
              'score' => $game->away_team_score,
              'scores' => $away_scores,
              'weight' => 'weight_2',
              'vote_case' => Vote::$VOTE_CASE_LOSS
            ];

          } else {
            $first = (object)[
              'team' => $game->away_team,
              'score' => $game->away_team_score,
              'scores' => $away_scores,
              'weight' => 'weight_2',
              'vote_case' => Vote::$VOTE_CASE_LOSS
            ];
            $second = (object)[
              'team' => $game->home_team,
              'score' => $game->home_team_score,
              'scores' => $away_scores,
              'weight' => 'weight_1',
              'vote_case' => Vote::$VOTE_CASE_WIN
            ];
          }
        ?>
          <tr>
            <td>
              <div class="row">
                <div class="col-xl-3 col-sm-6">
                  <div class="mb-2 text-muted">
                    <small><b>
                      <?php
                        $geoip = Geoip::getGeoip();
                        // echo Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at, 'UTC')->setTimezone($geoip ? $geoip->time_zone : 'UTC')->format('d M Y');
                        // if (!$geoip) {
                        //   echo ' UTC';
                        // }
                        echo Geoip::getLocalizedDate($game->start_at, 'date_no_year');
                      ?>
                    </b></small>
                  </div>
                  <div class="team">
                    <div class="team-logo">
                      <img src="<?php echo $first->team->logo; ?>" />
                    </div>
                    <div class="team-name<?php echo ($first->score > $second->score? ' bold' : ''); ?>">
                      <?php echo $first->team->short_name; ?>
                      <?php if ($game->between_players) : ?>
                        <div class="turn-scores"><?php echo $first->scores; ?></div>
                      <?php else : ?>
                        <span class="ml-2"><?php echo $first->score; ?></span>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="team">
                    <div class="team-logo">
                      <img src="<?php echo $second->team->logo; ?>" />
                    </div>
                    <div class="team-name<?php echo ($second->score > $first->score? ' bold' : ''); ?>">
                      <?php echo $second->team->short_name; ?>
                      <?php if ($game->between_players) : ?>
                        <div class="turn-scores"><?php echo $second->scores; ?></div>
                      <?php else : ?>
                        <span class="ml-2"><?php echo $second->score; ?></span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
                <?php
                  $votes = Vote::where(['game_id' => $game->id, 'user_id' => $voter->id])->get();
                  foreach($votes as $vote) : ?>
                  <div class="col-xl-3 col-lg-6">
                    <div class="mb-2">
                      <b>
                        <?php echo $vote->bet_type->name; ?>
                        <?php if ($vote->bet_type->value === BetType::$VALUE_OVER_UNDER) : echo "({$vote->game_bet_type->weight_1})"; endif; ?>
                      </b>
                    </div>
                    <?php if ($vote->bet_type->value === BetType::$VALUE_SPREAD) : ?>
                      <div class="pb-4">
                        <div class="pt-1">
                          <?php echo Format::formatSignedWeight($vote->game_bet_type[$first->weight]); ?>
                          <?php if (!$game->is_nulled && $vote->vote_case === $first->vote_case) : ?>
                            <span <?php if (!empty($vote->calculated_at) && $vote->matched !== null) : ?> class="<?php echo $vote->matched ? "text-success" : "text-danger"; ?>" <?php endif; ?>>
                              ▣ <b><?php if (!empty($vote->calculated_at)) : echo $vote->score; endif ?></b>
                            </span>
                          <?php endif; ?>
                        </div>
                        <div class="pt-1">
                          <?php echo Format::formatSignedWeight($vote->game_bet_type[$second->weight]); ?>
                          <?php if (!$game->is_nulled && $vote->vote_case === $second->vote_case) : ?>
                            <span <?php if (!empty($vote->calculated_at) && $vote->matched !== null) : ?> class="<?php echo $vote->matched ? "text-success" : "text-danger"; ?>" <?php endif; ?>>
                              ▣ <b><?php if (!empty($vote->calculated_at)) : echo $vote->score; endif ?></b>
                            </span>
                          <?php endif; ?>
                        </div>
                      </div>
                    <?php endif; ?>

                    <?php if ($vote->bet_type->value === BetType::$VALUE_MONEYLINE) : ?>
                      <div class="pb-4">
                        <div class="pt-1">
                          <?php echo Format::formatSignedWeight($vote->game_bet_type[$first->weight]); ?>
                          <?php if ($vote->vote_case === $first->vote_case) : ?>
                            <span <?php if(!empty($vote->calculated_at) && $vote->matched !== null) : ?> class="<?php echo $vote->matched ? "text-success" : "text-danger"; ?>" <?php endif; ?>>
                              ▣ <b><?php if (!empty($vote->calculated_at)) : echo $vote->score; endif ?></b>
                            </span>
                          <?php endif; ?>
                        </div>
                        <div class="pt-1">
                          <?php echo Format::formatSignedWeight($vote->game_bet_type[$second->weight]); ?>
                          <?php if ($vote->vote_case === $second->vote_case) : ?>
                            <span <?php if (!empty($vote->calculated_at) && $vote->matched !== null) : ?> class="<?php echo $vote->matched ? "text-success" : "text-danger"; ?>" <?php endif; ?>>
                              ▣ <b><?php if (!empty($vote->calculated_at)) : echo $vote->score; endif ?></b>
                            </span>
                          <?php endif; ?>
                        </div>
                        <?php if (!empty($vote->game_bet_type->weight_3)) :?>
                          <div class="pt-1">
                            <?php echo Format::formatSignedWeight($vote->game_bet_type->weight_3); ?>
                            <?php if ($vote->vote_case === Vote::$VOTE_CASE_TIE) : ?>
                              <span <?php if (!empty($vote->calculated_at) && $vote->matched !== null) : ?> class="<?php echo $vote->matched ? "text-success" : "text-danger"; ?>" <?php endif; ?>>
                                ▣ <b><?php if (!empty($vote->calculated_at)) : echo $vote->score; endif ?></b>
                              </span>
                            <?php endif; ?>
                          </div>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>

                    <?php if ($vote->bet_type->value === BetType::$VALUE_OVER_UNDER) : ?>
                      <div class="pb-4">
                        <div class="pt-1">
                          <?php echo trans('app.over'); ?>
                          <?php if (!$game->is_nulled && $vote->vote_case === Vote::$VOTE_CASE_WIN) : ?>
                            <span <?php if (!empty($vote->calculated_at) && $vote->matched !== null) : ?> class="<?php echo $vote->matched ? "text-success" : "text-danger"; ?>" <?php endif; ?>>
                              ▣ <b><?php if (!empty($vote->calculated_at)) : echo $vote->score; endif ?></b>
                            </span>
                          <?php endif; ?>
                        </div>
                        <div class="pt-1">
                          <?php echo trans('app.under'); ?>
                          <?php if (!$game->is_nulled && $vote->vote_case === Vote::$VOTE_CASE_LOSS) : ?>
                            <span <?php if (!empty($vote->calculated_at) && $vote->matched !== null) : ?> class="<?php echo $vote->matched ? "text-success" : "text-danger"; ?>" <?php endif; ?>>
                              ▣ <b><?php if (!empty($vote->calculated_at)) : echo $vote->score; endif ?></b>
                            </span>
                          <?php endif; ?>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>
                <?php  endforeach; ?>
              </div>
            </td>
          </tr>
          <?php $i++; ?>
          <?php if ($i % 4 == 0) : ?>
          <tr>
            <td>
              <?php 
                $banner_settings = (object)[
                  'banner_name' => 'game',
                  'style' => 'display: inline-block; width: 100%; height: 90px;',
                ];
                require(dirname(__FILE__) . '/../widgets/banner.php');
              ?>
            </td>
          </tr>
          <?php endif; ?>
        <?php endforeach; ?>
      </table>
      <p></p>
      <?php
        $query = array_except(Request::query(), 'page1');
        $query['tab'] = 'games';
        echo $games->appends($query)->links('pagination::simple-bootstrap-4');
      ?>
    </div>
    <div class="events tabPanel <?php echo $tab == 'events' ? 'active' : '' ?>">
      <table class="table table-striped table-bordered">
        <?php foreach($events as $event) : ?>
        <?php if ($event->vote) : ?>
        <tr>
          <td>
            <a href="<?php echo $event->page->locale_url; ?>">
              <?php echo $event->page->locale_name; ?>
            </a>
          </td>
        </tr>
        <tr>
          <td>
            <div class="row">
              <div class="col-xl-3 col-sm-6">
                <div class="mb-2 text-muted">
                  <small><b>
                    <?php
                      $geoip = Geoip::getGeoip();
                      echo Geoip::getLocalizedDate($event->page->start_at, 'date_no_year');
                    ?>
                  </b></small>
                </div>
                <div class="team">
                  <div class="team-logo">
                    <?php if ($event->candidate1->logo) : ?>
                      <img src="<?php echo $event->candidate1->logo; ?>" />
                    <?php endif; ?>
                  </div>
                  <div class="team-name<?php echo ($event->candidate1_score > $event->candidate2_score ? ' bold' : ''); ?>">
                    <?php echo $event->candidate1->name; ?>
                    <span class="ml-2">
                      <?php if ($event->calculated == 1) echo $event->candidate1_score; ?>
                    </span>
                  </div>
                </div>
                <div class="team">
                  <div class="team-logo">
                    <?php if ($event->candidate2->logo) : ?>
                      <img src="<?php echo $event->candidate2->logo; ?>" />
                    <?php endif; ?>
                  </div>
                  <div class="team-name<?php echo ($event->candidate2_score > $event->candidate1_score ? ' bold' : ''); ?>">
                    <?php echo $event->candidate2->name; ?>
                    <span class="ml-2">
                      <?php if ($event->calculated == 1) echo $event->candidate2_score; ?>
                    </span>
                  </div>
                </div>
              </div>
              <?php foreach($event->vote as $vote) : ?>
                <div class="col-xl-3 col-sm-6">
                  <div class="mb-2">
                    <b>
                      <?php echo $vote->name; ?>
                      <?php if ($vote->bet_type->value === BetType::$VALUE_OVER_UNDER) : echo "({$vote->event->over_under})"; endif; ?>
                    </b>
                  </div>

                  <?php if ($vote->value === BetType::$VALUE_SPREAD) : ?>
                    <div class="pb-4">
                      <div class="pt-1">
                        <?php echo Format::formatSignedWeight($event->spread); ?>
                        <?php if ($vote->vote_case === 'win') : ?>
                          <span <?php if (!empty($vote->calculated_at) && $vote->matched !== null) : ?> class="<?php echo $vote->matched ? "text-success" : "text-danger"; ?>" <?php endif; ?>>
                            ▣ <b><?php if (!empty($vote->calculated_at)) : echo $vote->score; endif ?></b>
                          </span>
                        <?php endif; ?>
                      </div>
                      <div class="pt-1">
                        <?php echo Format::formatSignedWeight('-' . $event->spread); ?>
                        <?php if ($vote->vote_case === 'loss') : ?>
                          <span <?php if (!empty($vote->calculated_at) && $vote->matched !== null) : ?> class="<?php echo $vote->matched ? "text-success" : "text-danger"; ?>" <?php endif; ?>>
                            ▣ <b><?php if (!empty($vote->calculated_at)) : echo $vote->score; endif ?></b>
                          </span>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endif; ?>

                  <?php if ($vote->value === BetType::$VALUE_MONEYLINE) : ?>
                    <div class="pb-4">
                      <div class="pt-1">
                        <?php echo Format::formatSignedWeight($event->candidate1_odds); ?>
                        <?php if ($vote->vote_case === 'win') : ?>
                          <span <?php if(!empty($vote->calculated_at) && $vote->matched !== null) : ?> class="<?php echo $vote->matched ? "text-success" : "text-danger"; ?>" <?php endif; ?>>
                            ▣ <b><?php if (!empty($vote->calculated_at)) : echo $vote->score; endif ?></b>
                          </span>
                        <?php endif; ?>
                      </div>
                      <div class="pt-1">
                        <?php echo Format::formatSignedWeight($event->candidate2_odds); ?>
                        <?php if ($vote->vote_case === 'loss') : ?>
                          <span <?php if (!empty($vote->calculated_at) && $vote->matched !== null) : ?> class="<?php echo $vote->matched ? "text-success" : "text-danger"; ?>" <?php endif; ?>>
                            ▣ <b><?php if (!empty($vote->calculated_at)) : echo $vote->score; endif ?></b>
                          </span>
                        <?php endif; ?>
                      </div>
                      <?php if (!empty($vote->tie_odds)) :?>
                        <div class="pt-1">
                          <?php echo Format::formatSignedWeight($vote->tie_odds); ?>
                          <?php if ($vote->vote_case === Vote::$VOTE_CASE_TIE) : ?>
                            <span <?php if (!empty($vote->calculated_at) && $vote->matched !== null) : ?> class="<?php echo $vote->matched ? "text-success" : "text-danger"; ?>" <?php endif; ?>>
                              ▣ <b><?php if (!empty($vote->calculated_at)) : echo $vote->score; endif ?></b>
                            </span>
                          <?php endif; ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>

                  <?php if ($vote->value === BetType::$VALUE_OVER_UNDER) : ?>
                    <div class="pb-4">
                      <div class="pt-1">
                        <?php echo trans('app.over'); ?>
                        <?php if ($vote->vote_case === 'win') : ?>
                          <span <?php if (!empty($vote->calculated_at) && $vote->matched !== null) : ?> class="<?php echo $vote->matched ? "text-success" : "text-danger"; ?>" <?php endif; ?>>
                            ▣ <b><?php if (!empty($vote->calculated_at)) : echo $vote->score; endif ?></b>
                          </span>
                        <?php endif; ?>
                      </div>
                      <div class="pt-1">
                        <?php echo trans('app.under'); ?>
                        <?php if ($vote->vote_case === 'loss') : ?>
                          <span <?php if (!empty($vote->calculated_at) && $vote->matched !== null) : ?> class="<?php echo $vote->matched ? "text-success" : "text-danger"; ?>" <?php endif; ?>>
                            ▣ <b><?php if (!empty($vote->calculated_at)) : echo $vote->score; endif ?></b>
                          </span>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </td>
        </tr>
        <?php endif; ?>
        <?php endforeach; ?>
      </table>
      <p></p>
      <?php
        $query = array_except(Request::query(), 'page2');
        $query['tab'] = 'events';
        echo $events->appends($query)->links('pagination::simple-bootstrap-4');
      ?>
    </div>
    <div class="futures tabPanel <?php echo $tab == 'futures' ? 'active' : '' ?>">
      <table class="table table-striped table-bordered">
        <?php foreach ($futures as $future) : ?>
        <tr>
          <td>
            <a href="<?php echo $future->page->locale_url; ?>">
              <?php echo $future->page->locale_name; ?>
            </a>
          </td>
        </tr>
        <tr>
          <td>
            <div class="row">
              <div class="col-sm-12">
                <div class="mb-2 text-muted">
                  <small><b>
                    <?php
                      $geoip = Geoip::getGeoip();
                      echo Geoip::getLocalizedDate($future->page->start_at, 'date_no_year');
                    ?>
                  </b></small>
                </div>
                <?php if ($future->winner !== null) : ?>
                <div class="team">
                  <div class="team-name bold">
                    <?php echo $future->winner->name; ?>
                    <span class="ml-2"><?php echo $future->winner->odds; ?></span>
                  </div>
                </div>
                <?php endif; ?>
                <div class="team">
                  <div class="team-name">
                    <?php echo $future->vote->name; ?>
                    <span class="ml-2"><?php echo $future->vote->odds; ?></span>
                  </div>
                  <div class="pl-2">
                    <?php if (!empty($future->calculated_at) && $future->matched !== null) : ?>
                      <span class="<?php echo $future->matched ? "text-success" : "text-danger"; ?>">
                        ▣ <b><?php echo $future->score; ?></b>
                      </span>
                    <?php else : ?>
                      <span>▣</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
      <p></p>
      <?php
        $query = array_except(Request::query(), 'page3');
        $query['tab'] = 'futures';
        echo $futures->appends($query)->links('pagination::simple-bootstrap-4');
      ?>
    </div>
  </div>
</div>