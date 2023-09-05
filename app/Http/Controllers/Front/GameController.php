<?php

namespace App\Http\Controllers\Front;

use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Game;
use App\GameBetType;
use App\GameVote;
use App\Vote;
use App\Leaderboard;
use App\User;
use App\League;
use App\Facades\Data;

class GameController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        // $this->middleware('auth');
    }

    public function vote(Request $request, String $game_id)
    {
        $user = Data::getUser();
        $game = Game::findOrFail($game_id);
        if (($game->status !== Game::$STATUS_NOT_STARTED && $game->status !== Game::$STATUS_POSTPONED)
        || $game->stop_voting || $game->start_at < (new Carbon())->format('Y-m-d H:i:s')) {
            return view('front.snippets.game', [
                'game' => $game,
                'league' => League::find($game->league_id),
                'user' => $user,
                'active' => true,
                'vote_error' => 'Cannot vote! Please refresh the page'
            ]);
        }
        $votesParams = $request->input('votes', []);

        $voterCount = 0;
        $voteCount = 0;

        foreach ($votesParams as $voteParams) {
            $gameBetType = GameBetType::findOrFail($voteParams['game_bet_type_id']);
            $vote = Vote::where(['game_bet_type_id' => $gameBetType->id, 'user_id' => $user->id])->first();

            if (!$vote) {
                if (!isset($voteParams['vote_case'])) {
                    continue;
                }
                $vote_case = $voteParams['vote_case'];
                $vote = Vote::create([
                    'game_bet_type_id' => $gameBetType->id,
                    'game_id' => $gameBetType->game_id,
                    'bet_type_id' => $gameBetType->bet_type_id,
                    'user_id' => $user->id,
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
            if ($vote) {
                if ($user->role !== User::$ROLE_UNKNOWN) {
                    Leaderboard::addVoteCount($game, $vote);
                }
            }
            $voteCount ++;
            $voterCount = 1;
        }

        $game->voter_count += $voterCount;
        $game->vote_count += $voteCount;
        $game->save();
        $game = Game::find($game_id);

        return view('front.snippets.game', [
            'game' => $game,
            'league' => League::find($game->league_id),
            'user' => $user,
            'active' => true
        ]);
    }
}
