<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\GameBetType;
use App\GameVote;
use App\ManualEventBetType;
use App\ManualEventVote;
use App\ManualFuture;
use App\ManualFutureAnswer;
use App\ManualFutureVote;
use App\User;
use App\Vote;

use DB;

use Log;

/**
 * Class ClearVote
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class ClearVoteCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "game:clearvote";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Clear Auto Vote";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $robots = User::where('robot', 1)->get();

      $ids = array();
      foreach ($robots as $robot) {
        array_push($ids, $robot->id);
      }
      
      /* Clear Game Auto Vote */
      $betIDs = array();
      $votes = Vote::whereIn('user_id', $ids)
                  ->select(DB::raw('DISTINCT(game_bet_type_id) AS game_bet_type_id'))
                  ->get();
      
      foreach ($votes as $vote) {
        array_push($betIDs, $vote->game_bet_type_id);
      }

      Vote::whereIn('user_id', $ids)->delete();

      $counts = Vote::whereIn('game_bet_type_id', $betIDs)
                      ->select('game_bet_type_id', 'vote_case', 'bet_type_id',
                              DB::raw('COUNT(vote_case) AS val'))
                      ->groupBy('game_bet_type_id', 'vote_case')
                      ->get();

      $bet_counts = [];
      foreach ($counts as $count) {
        if (empty($bet_counts[$count->game_bet_type_id])) {
          $bet_counts[$count->game_bet_type_id] = array(
            Vote::$VOTE_CASE_WIN => 0,
            Vote::$VOTE_CASE_LOSS => 0,
            Vote::$VOTE_CASE_TIE => 0,
            'bet_type_id' => $count->bet_type_id
          );
        }

        $bet_counts[$count->game_bet_type_id][$count->vote_case] += $count->val;
      }

      foreach ($bet_counts as $key => $bet_count) {
        GameVote::where('game_bet_type_id', $key)
                ->where('bet_type_id', $bet_count['bet_type_id'])
                ->update(array(
                  'win_vote_count' => $bet_count[Vote::$VOTE_CASE_WIN],
                  'loss_vote_count' => $bet_count[Vote::$VOTE_CASE_LOSS],
                  'tie_vote_count' => $bet_count[Vote::$VOTE_CASE_TIE]
                ));
      }

      /* Clear Manual Evetns Auto Vote */
      $betIDs = array();
      $votes = ManualEventVote::whereIn('user_id', $ids)
                  ->select(DB::raw('DISTINCT(event_bet_type_id) AS event_bet_type_id'))
                  ->get();

      foreach ($votes as $vote) {
        array_push($betIDs, $vote->event_bet_type_id);
      }

      ManualEventVote::whereIn('user_id', $ids)->delete();

      $counts = ManualEventVote::whereIn('event_bet_type_id', $betIDs)
                        ->select('event_bet_type_id', 'event_id', 'bet_type_id', 'vote_case',
                                DB::raw('COUNT(vote_case) AS val'))
                        ->groupBy('event_bet_type_id', 'vote_case')
                        ->get();

      $bet_counts = [];
      foreach ($counts as $count) {
        if (empty($bet_counts[$count->event_bet_type_id])) {
          $bet_counts[$count->event_bet_type_id] = array(
            Vote::$VOTE_CASE_WIN => 0,
            Vote::$VOTE_CASE_LOSS => 0,
            Vote::$VOTE_CASE_TIE => 0
          );
        }

        $bet_counts[$count->event_bet_type_id][$count->vote_case] += $count->val;
      }
      
      foreach ($bet_counts as $key => $bet_count) {
        ManualEventBetType::where('id', $key)
                      ->update(array(
                        'win_vote_count' => $bet_count['win'],
                        'loss_vote_count' => $bet_count['loss'],
                        'tie_vote_count' => $bet_count['tie']
                      ));
      }

      /* Clear Manual Futures Auto Vote */
      ManualFutureVote::whereIn('user_id', $ids)->delete();

      $futures = ManualFuture::get();
      foreach ($futures as $future) {
        ManualFuture::where('id', $future->id)
                    ->update(array(
                      'voter_count' => ManualFutureVote::where('future_id', $future->id)->count()
                    ));
      }

      $answers = ManualFutureAnswer::get();
      foreach ($answers as $answer) {
        ManualFutureAnswer::where('id', $answer->id)
                          ->update(array(
                            'vote_count' => ManualFutureVote::where('answer_id', $answer->id)->count()
                          ));
      }
    }
}
