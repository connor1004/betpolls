<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\BetType;
use App\Game;
use App\GameBetType;
use App\GameVote;
use App\ManualEvent;
use App\ManualEventBetType;
use App\ManualEventVote;
use App\ManualFuture;
use App\ManualFutureAnswer;
use App\ManualFutureVote;
use App\ManualPollPage;
use App\User;
use App\Vote;

use DB;

use Log;

/**
 * Class deletePostsCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class GameAutoVoteCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "game:autovote";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Auto Vote of the game";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $robots = User::where('robot', 1)->get();

      $games = Game::where('status', Game::$STATUS_NOT_STARTED)
                  ->where('stop_voting', 0)
                  ->where('start_at', '>', (new Carbon())->format('Y-m-d H:i:s'))
                  ->select('id')
                  ->get();

      $gameIDs = array();
      foreach ($games as $game) {
        array_push($gameIDs, $game->id);
      }

      $events = ManualEvent::leftJoin('manual_poll_pages', 'manual_poll_pages.id', '=', 'manual_events.page_id')
                ->where('manual_poll_pages.status', ManualPollPage::$STATUS_NOT_STARTED)
                ->where('manual_poll_pages.start_at', '>', (new Carbon())->format('Y-m-d H:i:s'))
                ->where('manual_poll_pages.published', 1)
                ->where('manual_events.published', 1)
                ->select('manual_events.id')
                ->get();

      $eventIDs = array();
      foreach ($events as $event) {
        array_push($eventIDs, $event->id);
      }

      $futures = ManualFuture::leftJoin('manual_poll_pages', 'manual_poll_pages.id', '=', 'manual_futures.page_id')
                ->where('manual_poll_pages.status', ManualPollPage::$STATUS_NOT_STARTED)
                ->where('manual_poll_pages.start_at', '>', (new Carbon())->format('Y-m-d H:i:s'))
                ->where('manual_poll_pages.published', 1)
                ->where('manual_futures.published', 1)
                ->select('manual_futures.id')
                ->get();

      $futureIDs = array();
      foreach ($futures as $future) {
        array_push($futureIDs, $future->id);
      }

      foreach ($robots as $robot) {
        /* Auto vote for games */
        $voteIDs = $gameIDs;

        $votes = Vote::where('user_id', $robot->id)
                    ->whereIn('game_id', $gameIDs)
                    ->select(DB::raw('DISTINCT(game_id) AS game_id'))
                    ->get();

        foreach ($votes as $vote) {
          unset($voteIDs[array_search($vote->game_id, $voteIDs)]);
        }

        $gameBetTypes = GameBetType::whereIn('game_id', $voteIDs)
                              ->where(function ($query) {
                                $query->where('weight_1', '!=', 0)
                                      ->orWhere('weight_2', '!=', 0)
                                      ->orWhere('weight_3', '!=', 0);
                              })
                              ->get();

        foreach ($gameBetTypes as $gameBetType) {
          $vote_case = '';

          if ($gameBetType->weight_3 > 0) {
            $rand = rand(0, 2);

            if ($rand == 0) {
              $vote_case = 'loss';
            }
            if ($rand == 1) {
              $vote_case = 'tie';
            }
            if ($rand == 2) {
              $vote_case = 'win';
            }
          } else {
            $rand = rand(0, 1);

            if ($rand == 0) {
              $vote_case = 'loss';
            } else {
              $vote_case = 'win';
            }
          }

          $vote = Vote::create([
            'game_bet_type_id' => $gameBetType->id,
            'game_id' => $gameBetType->game_id,
            'bet_type_id' => $gameBetType->bet_type_id,
            'user_id' => $robot->id,
            'vote_case' => $vote_case
          ]);

          $gameVote = GameVote::where('game_bet_type_id', $gameBetType->id)->first();

          if ($gameVote) {
            switch ($vote_case) {
              case Vote::$VOTE_CASE_WIN:
                $gameVote->win_vote_count++;
                break;
              case Vote::$VOTE_CASE_LOSS:
                $gameVote->loss_vote_count++;
                break;
              case Vote::$VOTE_CASE_TIE:
                $gameVote->tie_vote_count++;
                break;
              default:
                break;
            }
            $gameVote->save();
          } else {
            $gameVote = GameVote::create([
              'game_bet_type_id' => $gameBetType->id,
              'game_id' => $gameBetType->game_id,
              'bet_type_id' => $gameBetType->bet_type_id,
              'win_vote_count' => $vote_case === Vote::$VOTE_CASE_WIN ? 1 : 0,
              'loss_vote_count' => $vote_case === Vote::$VOTE_CASE_LOSS ? 1 : 0,
              'tie_vote_count' => $vote_case === Vote::$VOTE_CASE_TIE ? 1 : 0
            ]);
          }
        }

        /* Auto vote for manual events */
        $eventVoteIDs = $eventIDs;

        $eventVotes = ManualEventVote::where('user_id', $robot->id)
                    ->whereIn('event_id', $eventIDs)
                    ->select(DB::raw('DISTINCT(event_id) AS event_id'))
                    ->get();

        foreach ($eventVotes as $vote) {
          unset($eventVoteIDs[array_search($vote->event_id, $eventVoteIDs)]);
        }
        
        $eventBetTypes = ManualEventBetType::leftJoin('bet_types', 'bet_types.id', '=', 'manual_event_bet_types.bet_type_id')
              ->leftJoin('manual_events', 'manual_events.id', '=', 'manual_event_bet_types.event_id')
              ->whereIn('event_id', $eventVoteIDs)
              ->select('manual_event_bet_types.*', 'bet_types.value',
                       'manual_events.candidate1_odds', 'manual_events.candidate2_odds',
                       'manual_events.spread', 'manual_events.over_under', 'manual_events.tie_odds')
              ->get();
        
        foreach ($eventBetTypes as $eventBetType) {
          $vote_case = '';

          $valid = true;
          if ($eventBetType->bet_type_id == 1 && $eventBetType->spread == 0) {
            $valid = false;
          }
          if ($eventBetType->bet_type_id == 2 &&
              $eventBetType->candidate1_odds == 0 && $eventBetType->candidate2_odds == 0 &&
              $eventBetType->tie_odds == 0
             ) {
            $valid = false;
          }
          if ($eventBetType->bet_type_id == 3 && $eventBetType->over_under == 0) {
            $valid = false;
          }

          if (!$valid) {
            continue;
          }

          if ($eventBetType->value == Vote::$VOTE_CASE_TIE && $eventBetType->tie_odds > 0) {
            $rand = rand(0, 2);

            if ($rand == 0) {
              $vote_case = 'loss';
            }
            if ($rand == 1) {
              $vote_case = 'tie';
            }
            if ($rand == 2) {
              $vote_case = 'win';
            }
          } else {
            $rand = rand(0, 1);

            if ($rand == 0) {
              $vote_case = 'loss';
            } else {
              $vote_case = 'win';
            }
          }

          $vote = ManualEventVote::create([
            'event_bet_type_id' => $eventBetType->id,
            'event_id' => $eventBetType->event_id,
            'page_id' => $eventBetType->page_id,
            'bet_type_id' => $eventBetType->bet_type_id,
            'user_id' => $robot->id,
            'vote_case' => $vote_case
          ]);

          switch ($vote_case) {
            case Vote::$VOTE_CASE_WIN:
              $eventBetType->win_vote_count++;
              break;
            case Vote::$VOTE_CASE_LOSS:
              $eventBetType->loss_vote_count++;
              break;
            case Vote::$VOTE_CASE_TIE:
              $eventBetType->tie_vote_count++;
              break;
            default:
              break;
          }

          $eventBetType->save();
        }

        /* Auto vote for manual futures */
        $futureVoteIDs = $futureIDs;

        $futureVotes = ManualFutureVote::where('user_id', $robot->id)
                    ->whereIn('future_id', $futureIDs)
                    ->select(DB::raw('DISTINCT(future_id) AS future_id'))
                    ->get();
                    
        foreach ($futureVotes as $vote) {
          unset($futureVoteIDs[array_search($vote->future_id, $futureVoteIDs)]);
        }

        foreach ($futureVoteIDs as $id) {
          $futureAnswers = ManualFutureAnswer::where('future_id', $id)
                    ->select('id', 'page_id')
                    ->get();

          $answer_id = $futureAnswers[rand(0, sizeof($futureAnswers) - 1)]->id;
          
          ManualFutureVote::create([
            'page_id' => $futureAnswers[0]->page_id,
            'future_id' => $id,
            'user_id' => $robot->id,
            'answer_id' => $answer_id
          ]);

          ManualFuture::where('id', $id)->increment('voter_count');
          ManualFutureAnswer::where('id', $answer_id)->increment('vote_count');
        }
      }
    }
}
