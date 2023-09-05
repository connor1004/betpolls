<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Facades\Utils;
use App\Facades\Geoip;
use App\Facades\Calculation;
use App\Leaderboard;
use App\Point;
use App\SportCategory;
use App\League;
use App\User;
use App\Game;
use Carbon\Carbon;
use App\Vote;
use App\Post;
use App\ManualCategory;
use App\ManualSubcategory;
use App\ManualPollPage;
use App\ManualFutureVote;
use App\ManualEvent;
use App\ManualEventVote;
use App\ManualFuture;
use App\ManualFutureAnswer;
use DB;
use Log;

class LeaderboardController extends Controller
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

    public function index(Request $request)
    {
        $page = Post::where('slug', 'leaderboard')->where('post_type', Post::$POST_TYPE_PAGE)->first();
        $sport_category_id = $request->input('sport_category_id', 0);
        $dynamic_period = $request->input('dynamic_period', false);

        if (empty($sport_category_id)) {
            $sport_category_id = 0;
        }

        $league_id = $request->input('league_id', 0);
        if (empty($league_id)) {
            $league_id = 0;
        }

        if ($sport_category_id == 0) {
            $league_id = 0;
        }

        $type = $request->input('type', 0);
        if ($type == 0) {
            $sport_category_id = 0;
            $league_id = 0;
        }

        $static_period = true;

        $static_period_type = Leaderboard::$PERIOD_TYPE_WEEKLY;
        $period_type = $request->input('period_type', Leaderboard::$PERIOD_TYPE_7_DAYS);
        $sport_categories = [];
        $leagues = [];
        if ($type == 1) {
            $sport_categories = SportCategory::orderBy('name')->get();
            $leagues = League::where('sport_category_id', $sport_category_id)
                            ->orderBy('name')->get();
        } else if ($type == 2 || $type == 3) {
            $sport_categories = ManualCategory::orderBy('name')->get();
            $leagues = ManualSubcategory::where('category_id', $sport_category_id)
                            ->orderBy('name')->get();
        }

        $start_at = null;
        if (!$dynamic_period) {
            switch ($period_type) {
                case Leaderboard::$PERIOD_TYPE_FOREVER:
                    $start_at = (new Carbon())->setTimezone('America/New_York')->startOfCentury()->format('Y-m-d');
                    $end_at = (new Carbon())->setTimezone('America/New_York')->endOfCentury()->format('Y-m-d');
                    $static_period_type = Leaderboard::$PERIOD_TYPE_FOREVER;
                    break;
                case Leaderboard::$PERIOD_TYPE_YEARLY:
                    $start_at = (new Carbon())->setTimezone('America/New_York')->startOfYear()->format('Y-m-d');
                    $end_at = (new Carbon())->setTimezone('America/New_York')->endOfYear()->format('Y-m-d');
                    $static_period_type = Leaderboard::$PERIOD_TYPE_YEARLY;
                    break;
                case Leaderboard::$PERIOD_TYPE_MONTHLY:
                    $start_at = (new Carbon())->setTimezone('America/New_York')->startOfMonth()->format('Y-m-d');
                    $end_at = (new Carbon())->setTimezone('America/New_York')->endOfMonth()->format('Y-m-d');
                    $static_period_type = Leaderboard::$PERIOD_TYPE_MONTHLY;
                    break;
                case Leaderboard::$PERIOD_TYPE_WEEKLY:
                    $start_at = (new Carbon())->setTimezone('America/New_York')->startOfWeek()->format('Y-m-d');
                    $end_at = (new Carbon())->setTimezone('America/New_York')->endOfWeek()->format('Y-m-d');
                    $static_period_type = Leaderboard::$PERIOD_TYPE_WEEKLY;
                    break;
                case Leaderboard::$PERIOD_TYPE_RANKING:
                    $start_at = (new Carbon())->setTimezone('America/New_York')->startOfYear()->format('Y-m-d');
                    $end_at = $start_at;
                    $sport_category_id = 0;
                    $league_id = 0;
                    $static_period = false;
                    break;
                default:
                    $static_period = false;
                    break;
            }
        } else {
            $static_period = false;
            $start_at = $request->input('start_at', (new Carbon())->setTimezone('America/New_York')->format('Y-m-d'));
            $end_at = $request->input('end_at', (new Carbon())->setTimezone('America/New_York')->format('Y-m-d'));
        }

        if ($start_at && $period_type == Leaderboard::$PERIOD_TYPE_RANKING) {
            $leaderboards = Point::where([
                'type' => ($type == 2 || $type == 3) ? 2 : $type,
                'sport_category_id' => $sport_category_id,
                'league_id' => $league_id,
                'start_at' => $start_at
            ])
            ->whereHas('user', function ($query) {
                $query->where('role', '!=', User::$ROLE_UNKNOWN);
            })
            ->orderBy('score', 'DESC')->orderBy('user_id')->paginate(30);
        } else {
            if ($dynamic_period || $static_period) {
                $start_at = Carbon::createFromFormat('Y-m-d', $start_at, 'America/New_York')->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
                $end_at = Carbon::createFromFormat('Y-m-d', $end_at, 'America/New_York')->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            } else {
                $start_at = (new Carbon(null))->setTimezone('America/New_York')->startOfDay()->subDays((int)$period_type)->setTimezone('UTC')->format('Y-m-d H:i:s');
                $end_at = (new Carbon(null))->setTimezone('America/New_York')->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            }

            $leaderboardsA = null;
            $leaderboardsM = null;
            if ($type == 0 || $type == 1) {
                $leaderboardsA = Vote::join('games', 'votes.game_id', '=', 'games.id')
                    ->join('leagues', 'games.league_id', '=', 'leagues.id')
                    ->select('votes.user_id', DB::raw('SUM(score) AS score'))
                    ->whereBetween('games.start_at', [$start_at, $end_at])
                    ->whereNotNull('games.calculated_at')
                    ->whereNotNull('games.calculating_at')
                    ->whereHas('user', function ($query) {
                        $query->where('role', '!=', User::$ROLE_UNKNOWN);
                    });

                if ($league_id !== 0) {
                    $leaderboardsA = $leaderboardsA->where('games.league_id', $league_id);
                } elseif ($sport_category_id !== 0) {
                    $leaderboardsA = $leaderboardsA->where('leagues.sport_category_id', $sport_category_id);
                }
                $leaderboardsA = $leaderboardsA->groupBy('votes.user_id');
            }
            if ($type == 0 || $type == 2) {
                if ($type == 0) {
                    $leaderboardsF = ManualFutureVote::join('manual_poll_pages', 'manual_future_votes.page_id', '=', 'manual_poll_pages.id')
                        ->select('user_id', 'score')
                        ->whereBetween('manual_poll_pages.start_at', [$start_at, $end_at])
                        ->whereNotNull('manual_poll_pages.calculated_at')
                        ->whereNotNull('manual_poll_pages.calculating_at')
                        ->whereHas('user', function ($query) {
                            $query->where('role', '!=', User::$ROLE_UNKNOWN);
                        });
                }

                $leaderboardsE = ManualEventVote::join('manual_poll_pages', 'manual_event_votes.page_id', '=', 'manual_poll_pages.id')
                    ->select('user_id', 'score')
                    ->whereBetween('manual_poll_pages.start_at', [$start_at, $end_at])
                    ->whereNotNull('manual_poll_pages.calculated_at')
                    ->whereNotNull('manual_poll_pages.calculating_at')
                    ->whereHas('user', function ($query) {
                        $query->where('role', '!=', User::$ROLE_UNKNOWN);
                    });

                if ($league_id !== 0) {
                    if ($type == 0) {
                        $leaderboardsF = $leaderboardsF->where('subcategory_id', $league_id);
                    }
                    $leaderboardsE = $leaderboardsE->where('subcategory_id', $league_id);
                } elseif ($sport_category_id !== 0) {
                    if ($type == 0) {
                        $leaderboardsF = $leaderboardsF->where('category_id', $sport_category_id);
                    }
                    $leaderboardsE = $leaderboardsE->where('category_id', $sport_category_id);
                }

                if ($type == 0) {
                    $leaderboardsM = $leaderboardsF->unionAll($leaderboardsE);
                } else {
                    $leaderboardsM = $leaderboardsE;
                }
                $leaderboardsM = DB::query()->fromSub($leaderboardsM, 'f_e')
                    ->select('user_id', DB::raw('SUM(score) AS score'))
                    ->groupBy('user_id');
            }
            if ($type == 0 || $type == 3) {
                $leaderboardsF = ManualFutureVote::join('manual_poll_pages', 'manual_future_votes.page_id', '=', 'manual_poll_pages.id')
                    ->select('user_id', 'score')
                    ->whereBetween('manual_poll_pages.start_at', [$start_at, $end_at])
                    ->whereNotNull('manual_poll_pages.calculated_at')
                    ->whereNotNull('manual_poll_pages.calculating_at')
                    ->whereHas('user', function ($query) {
                        $query->where('role', '!=', User::$ROLE_UNKNOWN);
                    });

                if ($type == 0) {
                    $leaderboardsE = ManualEventVote::join('manual_poll_pages', 'manual_event_votes.page_id', '=', 'manual_poll_pages.id')
                        ->select('user_id', 'score')
                        ->whereBetween('manual_poll_pages.start_at', [$start_at, $end_at])
                        ->whereNotNull('manual_poll_pages.calculated_at')
                        ->whereNotNull('manual_poll_pages.calculating_at')
                        ->whereHas('user', function ($query) {
                            $query->where('role', '!=', User::$ROLE_UNKNOWN);
                        });
                }

                if ($league_id !== 0) {
                    $leaderboardsF = $leaderboardsF->where('subcategory_id', $league_id);
                    if ($type == 0) {
                        $leaderboardsE = $leaderboardsE->where('subcategory_id', $league_id);
                    }
                } elseif ($sport_category_id !== 0) {
                    $leaderboardsF = $leaderboardsF->where('category_id', $sport_category_id);
                    if ($type == 0) {
                        $leaderboardsE = $leaderboardsE->where('category_id', $sport_category_id);
                    }
                }

                if ($type == 0) {
                    $leaderboardsM = $leaderboardsF->unionAll($leaderboardsE);
                } else {
                    $leaderboardsM = $leaderboardsF;
                }
                $leaderboardsM = DB::query()->fromSub($leaderboardsM, 'f_e')
                    ->select('user_id', DB::raw('SUM(score) AS score'))
                    ->groupBy('user_id');
            }
            
            if ($type == 1) {
                $leaderboards = $leaderboardsA;
            }
            else if ($type == 2 || $type == 3) {
                $leaderboards = $leaderboardsM;
            }
            else {
                $leaderboards = $leaderboardsA->unionAll($leaderboardsM);
                $leaderboards = DB::query()->fromSub($leaderboards, 'a_m')
                    ->select('user_id', DB::raw('SUM(score) as score'))
                    ->groupBy('user_id');
            }
            $leaderboards = $leaderboards->orderBy('score', 'DESC')->orderBy('user_id')->paginate(30);
        }

        if ($dynamic_period) {
            $start_at = $request->input('start_at', (new Carbon())->setTimezone('America/New_York')->format('Y-m-d'));
            $end_at = $request->input('end_at', (new Carbon())->setTimezone('America/New_York')->format('Y-m-d'));
        }

        if ($request->input('ajax')) {
            return view('front.snippets.leaderboard-table', [
                'static_period_type' => $static_period_type,
                'type' => $type,
                'sport_category_id' => $sport_category_id,
                'league_id' => $league_id,
                'period_type' => $period_type,
                'leaderboards' => $leaderboards,
                'sport_categories' => $sport_categories,
                'leagues' => $leagues,
                'static_period' => $static_period,
                'start_at' => $start_at,
                'end_at' => $end_at,
                'dynamic_period' => $dynamic_period
            ]);
        }

        return view('front.leaderboard', [
            'page' => $page,
            'canonical_link' => Utils::localeUrl('leaderboard'),
            'static_period_type' => $static_period_type,
            'type' => $type,
            'sport_category_id' => $sport_category_id,
            'league_id' => $league_id,
            'period_type' => $period_type,
            'leaderboards' => $leaderboards,
            'sport_categories' => $sport_categories,
            'leagues' => $leagues,
            'static_period' => $static_period,
            'start_at' => $start_at,
            'end_at' => $end_at,
            'dynamic_period' => $dynamic_period
        ]);
    }

    public function user(Request $request, String $slug)
    {
        $user = User::where('username', $slug)->firstOrFail();
        $date = new Carbon();

        $weekly_leaderboard = Calculation::calculateLeaderboard($user->id, 0, 0, 0, Leaderboard::$PERIOD_TYPE_WEEKLY, true);

        $monthly_leaderboard = Calculation::calculateLeaderboard($user->id, 0, 0, 0, Leaderboard::$PERIOD_TYPE_MONTHLY, true);

        $yearly_leaderboard = Calculation::calculateLeaderboard($user->id, 0, 0, 0, Leaderboard::$PERIOD_TYPE_YEARLY, true);

        $games = [];
        $games = Game::whereHas('votes', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->orderBy('start_at', 'DESC')->paginate(20, ['*'], 'page1');

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

        $locale = app('translator')->getLocale();
        
        if ($locale == 'es') {
            $title = $user->username . ' | Pronósticos Deportivos | Betpolls.com';
            $meta_description = 'Experto deportivo | ' . $user->username . 
            ' | Chequea sus pronósticos deportivos diaramente así como sus estadísticas semanales, ' .
            'mensuales y anuales para ver predicciones de apuestas gratis.';
        } else {
            $title = $user->username . ' | Fantasy Picks | Betpolls.com';
            $meta_description = 'Sports expert | ' . $user->username .
            ' | Checkout his daily sports picks and his weekly, ' .
            'monthly and yearly stats to get free betting tips.';
        }

        return view('front.user', [
            'page' => (object)[
                'title' => $title,
                'meta_description' => $meta_description
            ],
            'tab' => $request->input('tab', 'games'),
            'canonical_link' => $user->locale_url,
            'voter' => $user,
            'weekly_leaderboard' => $weekly_leaderboard,
            'monthly_leaderboard' => $monthly_leaderboard,
            'yearly_leaderboard' => $yearly_leaderboard,
            'games' => $games,
            'events' => $events,
            'futures' => $futures
        ]);
    }
}
