<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

use Symfony\Component\HttpFoundation\Cookie;
use Carbon\Carbon;
use App\Facades\Utils;
use App\Facades\Calculation;
use App\Mails\ConfirmationMail;
use App\Leaderboard;
use App\Game;
use App\ManualEvent;
use App\ManualEventVote;
use App\ManualFuture;
use App\ManualFutureAnswer;
use App\ManualFutureVote;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
        // $this->middleware('redirectIfUnconfirmed');
    }

    public function index(Request $request)
    {
        $sport_category_id = $request->input('sport_category_id', 0);
        $user = Auth::user();
        $date = new Carbon();

        $weekly_leaderboard = Calculation::calculateLeaderboard($user->id, 0, 0, 0, Leaderboard::$PERIOD_TYPE_WEEKLY, true);

        $monthly_leaderboard = Calculation::calculateLeaderboard($user->id, 0, 0, 0, Leaderboard::$PERIOD_TYPE_MONTHLY, true);

        $yearly_leaderboard = Calculation::calculateLeaderboard($user->id, 0, 0, 0, Leaderboard::$PERIOD_TYPE_YEARLY, true);

        // $weekly_start_at = (new Carbon($date))->startOfWeek()->format('Y-m-d');
        // $weekly_start_at = (new Carbon($date))->setTimezone('America/New_York')->startOfWeek()->format('Y-m-d');
        // $weekly_leaderboard = Leaderboard::where([
        //     'start_at' => $weekly_start_at,
        //     'period_type' => Leaderboard::$PERIOD_TYPE_WEEKLY,
        //     'user_id' => $user->id,
        //     'sport_category_id' => $sport_category_id,
        // ])->first();

        // $monthly_start_at = (new Carbon($date))->startOfMonth()->format('Y-m-d');
        // $monthly_start_at = (new Carbon($date))->setTimezone('America/New_York')->startOfMonth()->format('Y-m-d');
        // $monthly_leaderboard = Leaderboard::where([
        //     'start_at' => $monthly_start_at,
        //     'period_type' => Leaderboard::$PERIOD_TYPE_MONTHLY,
        //     'user_id' => $user->id,
        //     'sport_category_id' => $sport_category_id,
        // ])->first();

        // $yearly_start_at = (new Carbon($date))->startOfYear()->format('Y-m-d');
        // $yearly_start_at = (new Carbon($date))->setTimezone('America/New_York')->startOfYear()->format('Y-m-d');
        // $yearly_leaderboard = Leaderboard::where([
        //     'start_at' => $yearly_start_at,
        //     'period_type' => Leaderboard::$PERIOD_TYPE_YEARLY,
        //     'user_id' => $user->id,
        //     'sport_category_id' => $sport_category_id,
        // ])->first();

        $games = [];
        $games = Game::whereHas('votes', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->orderBy('start_at', 'DESC')->paginate(10, ['*'], 'page1');

        $events = ManualEvent::leftJoin('manual_poll_pages', 'manual_poll_pages.id', '=', 'manual_events.page_id')
            ->leftJoin('manual_candidates AS candidate1', 'candidate1.id', '=', 'manual_events.candidate1_id')
            ->leftJoin('manual_candidates AS candidate2', 'candidate2.id', '=', 'manual_events.candidate2_id')
            ->select(
                'manual_poll_pages.start_at', 'manual_events.*',
                'candidate1.name AS cand_name1', 'candidate1.logo AS cand_logo1',
                'candidate2.name AS cand_name2', 'candidate2.logo AS cand_logo2'
            )
            ->orderBy('manual_poll_pages.start_at', 'DESC')
            ->paginate(10, ['*'], 'page2');

        for ($i = 0; $i < sizeof($events); $i++) {
            $vote = ManualEventVote::leftJoin('bet_types', 'bet_types.id', '=', 'manual_event_votes.bet_type_id')
                ->where('event_id', $events[$i]->id)
                ->where('user_id', $user->id)
                ->select('manual_event_votes.*', 'bet_types.value', 'bet_types.name')
                ->get();
            
            if (sizeof($vote) > 0) {
                $events[$i]->vote = $vote;
            }
        }

        $futures = ManualFuture::leftJoin('manual_poll_pages', 'manual_poll_pages.id', '=', 'manual_futures.page_id')
            ->leftJoin('manual_future_votes', 'manual_future_votes.future_id', '=', 'manual_futures.id')
            ->where('manual_future_votes.user_id', $user->id)
            ->select(
                'manual_poll_pages.start_at', 'manual_futures.*',
                'manual_future_votes.answer_id', 'manual_future_votes.score', 'manual_future_votes.matched'
            )
            ->orderBy('manual_poll_pages.start_at', 'DESC')
            ->paginate(10, ['*'], 'page3');

        for ($i = 0; $i < sizeof($futures); $i++) {
            $winner = ManualFutureAnswer::leftJoin(
                'manual_candidates',
                'manual_candidates.id', '=', 'manual_future_answers.candidate_id'
            )
            ->where('manual_future_answers.score', 1)
            ->where('manual_future_answers.future_id', $futures[$i]->id)
            ->select('manual_future_answers.*', 'manual_candidates.name')
            ->first();

            $futures[$i]->winner = $winner;

            $vote = ManualFutureAnswer::leftJoin(
                    'manual_candidates',
                    'manual_candidates.id', '=', 'manual_future_answers.candidate_id'
                )
                ->where('manual_future_answers.id', $futures[$i]->answer_id)
                ->where('manual_future_answers.future_id', $futures[$i]->id)
                ->select('manual_future_answers.*', 'manual_candidates.name')
                ->first();

            $futures[$i]->vote = $vote;
        }
        
        return view('front.profile', [
            'voter' => $user,
            'tab' => $request->input('tab', 'games'),
            'weekly_leaderboard' => $weekly_leaderboard,
            'monthly_leaderboard' => $monthly_leaderboard,
            'yearly_leaderboard' => $yearly_leaderboard,
            'games' => $games,
            'events' => $events,
            'futures' => $futures
        ]);
    }

    public function pendingConfirmation()
    {
        return view('front.pending-confirmation');
    }

    public function sendConfirmation(Request $request)
    {
        $user = $request->user();
        Mail::to($user)->send(new ConfirmationMail($user));
        return redirect(Utils::localeUrl('pending-confirmation'));
    }

    public function confirm(Request $request)
    {
        $user = $request->user();
        if ($user->isValidConfirmCode($request->input('code'))) {
            $user->update(['confirmed' => true]);
            return redirect(Utils::localeUrl('profile'));
        }
        return redirect(Utils::localeUrl('pending-confirmation'));
    }

    public function logout()
    {
        $tokenCookie = new Cookie('token', '', time() - 60 * 60 * 48);
        return response(view('front.redirect', [
            'url' => Utils::localeUrl('/')
        ]))->header('Set-Cookie', $tokenCookie->__toString());
    }
}
