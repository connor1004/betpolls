<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Facades\Utils;
use App\Facades\Geoip;
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
use App\ManualEventVote;
use DB;
use Log;

class LeaderboardControllerOld extends Controller
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
            $sport_categories = SportCategory::orderBy('display_order', 'ASC')->get();
            $leagues = League::where('sport_category_id', $sport_category_id)
                            ->orderBy('display_order', 'ASC')->get();
        } else if ($type == 2) {
            $sport_categories = ManualCategory::orderBy('display_order', 'ASC')->get();
            $leagues = ManualSubcategory::where('category_id', $sport_category_id)
                            ->orderBy('display_order', 'ASC')->get();
        }

        $start_at = null;
        if (!$dynamic_period) {
            switch ($period_type) {
                case Leaderboard::$PERIOD_TYPE_FOREVER:
                    $start_at = (new Carbon())->setTimezone('America/New_York')->startOfCentury()->format('Y-m-d');
                    $static_period_type = Leaderboard::$PERIOD_TYPE_FOREVER;
                    break;
                case Leaderboard::$PERIOD_TYPE_YEARLY:
                    $start_at = (new Carbon())->setTimezone('America/New_York')->startOfYear()->format('Y-m-d');
                    $static_period_type = Leaderboard::$PERIOD_TYPE_YEARLY;
                    break;
                case Leaderboard::$PERIOD_TYPE_MONTHLY:
                    $start_at = (new Carbon())->setTimezone('America/New_York')->startOfMonth()->format('Y-m-d');
                    $static_period_type = Leaderboard::$PERIOD_TYPE_MONTHLY;
                    break;
                case Leaderboard::$PERIOD_TYPE_WEEKLY:
                    $start_at = (new Carbon())->setTimezone('America/New_York')->startOfWeek()->format('Y-m-d');
                    $static_period_type = Leaderboard::$PERIOD_TYPE_WEEKLY;
                    break;
                case Leaderboard::$PERIOD_TYPE_RANKING:
                    $start_at = (new Carbon())->setTimezone('America/New_York')->startOfYear()->format('Y-m-d');
                    $sport_category_id = 0;
                    $league_id = 0;
                    $static_period = false;
                    break;
                default:
                    $static_period = false;
                    break;
            }
            $end_at = $start_at;
        } else {
            $static_period = false;
            $start_at = $request->input('start_at', (new Carbon())->setTimezone('America/New_York')->format('Y-m-d'));
            $end_at = $request->input('end_at', (new Carbon())->setTimezone('America/New_York')->format('Y-m-d'));
        }

        if ($static_period) {
            $leaderboards = Leaderboard::where([
                'type' => $type,
                'sport_category_id' => $sport_category_id,
                'league_id' => $league_id,
                'period_type' => $period_type,
                'start_at' => $start_at
            ])->whereHas('user', function ($query) {
                $query->where('role', '!=', User::$ROLE_UNKNOWN);
            })->orderBy('score', 'DESC')->orderBy('user_id')->paginate(20);
        } else {
            if ($start_at && !$dynamic_period) {
                $leaderboards = Point::where([
                    'type' => $type,
                    'sport_category_id' => $sport_category_id,
                    'league_id' => $league_id,
                    'start_at' => $start_at
                ])
                ->whereHas('user', function ($query) {
                    $query->where('role', '!=', User::$ROLE_UNKNOWN);
                })
                ->orderBy('score', 'DESC')->orderBy('user_id')->paginate(20);
            } else {
                $geoip = Geoip::getGeoip();
                $timezone = $geoip ? $geoip->time_zone : 'UTC';
                if ($dynamic_period) {
                    // $start_at = Carbon::createFromFormat('Y-m-d', $start_at, $timezone)->setTimezone('UTC')->format('Y-m-d H:i:s');
                    // $end_at = Carbon::createFromFormat('Y-m-d', $end_at, $timezone)->setTimezone('UTC')->format('Y-m-d H:i:s');
                    $start_at = Carbon::createFromFormat('Y-m-d', $start_at, 'America/New_York')->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
                    $end_at = Carbon::createFromFormat('Y-m-d', $end_at, 'America/New_York')->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
                } else {
                    // $start_at = (new Carbon(null, $timezone))->startOfDay()->subDays((int)$period_type)->setTimezone('UTC')->format('Y-m-d H:i:s');
                    // $end_at = (new Carbon(null, $timezone))->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
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
                    //     ->orderBy('score', 'DESC')
                    //     ->paginate(20);
                }
                if ($type == 0 || $type == 2) {
                    $leaderboardsF = ManualFutureVote::join('manual_poll_pages', 'manual_future_votes.page_id', '=', 'manual_poll_pages.id')
                        ->select('user_id', 'score')
                        ->whereBetween('manual_future_votes.calculated_at', [$start_at, $end_at])
                        ->whereHas('user', function ($query) {
                            $query->where('role', '!=', User::$ROLE_UNKNOWN);
                        });

                    $leaderboardsE = ManualEventVote::join('manual_poll_pages', 'manual_event_votes.page_id', '=', 'manual_poll_pages.id')
                        ->select('user_id', 'score')
                        ->whereBetween('manual_event_votes.calculated_at', [$start_at, $end_at])
                        ->whereHas('user', function ($query) {
                            $query->where('role', '!=', User::$ROLE_UNKNOWN);
                        });    

                    if ($league_id !== 0) {
                        $leaderboardsF = $leaderboardsF->where('subcategory_id', $league_id);
                        $leaderboardsE = $leaderboardsE->where('subcategory_id', $league_id);
                    } elseif ($sport_category_id !== 0) {
                        $leaderboardsF = $leaderboardsF->where('category_id', $sport_category_id);
                        $leaderboardsE = $leaderboardsE->where('category_id', $sport_category_id);
                    }

                    $leaderboardsM = $leaderboardsF->unionAll($leaderboardsE);
                    $leaderboardsM = DB::query()->fromSub($leaderboardsM, 'f_e')
                        ->select('user_id', DB::raw('SUM(score) AS score'))
                        ->groupBy('user_id');
                }
                
                if ($type == 1) {
                    $leaderboards = $leaderboardsA;
                }
                else if ($type == 2) {
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
        $type = $request->input('type', 1);
        $user = User::where('username', $slug)->firstOrFail();
        $date = new Carbon();

        $weekly_start_at = (new Carbon($date))->setTimezone('America/New_York')->startOfWeek()->format('Y-m-d');
        $weekly_leaderboard = Leaderboard::where([
            'start_at' => $weekly_start_at,
            'period_type' => Leaderboard::$PERIOD_TYPE_WEEKLY,
            'user_id' => $user->id,
            'type' => 0,
        ])->first();

        $monthly_start_at = (new Carbon($date))->setTimezone('America/New_York')->startOfMonth()->format('Y-m-d');
        $monthly_leaderboard = Leaderboard::where([
            'start_at' => $monthly_start_at,
            'period_type' => Leaderboard::$PERIOD_TYPE_MONTHLY,
            'user_id' => $user->id,
            'type' => 0,
        ])->first();

        $yearly_start_at = (new Carbon($date))->setTimezone('America/New_York')->startOfYear()->format('Y-m-d');
        $yearly_leaderboard = Leaderboard::where([
            'start_at' => $yearly_start_at,
            'period_type' => Leaderboard::$PERIOD_TYPE_YEARLY,
            'user_id' => $user->id,
            'type' => 0,
        ])->first();

        $games = [];
        if ($type == 1) {
            $games = Game::whereHas('votes', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->orderBy('start_at', 'DESC')->paginate(20);
        }

        return view('front.user', [
            'page' => (object)[
                'title' => $user->username . ' | Betpolls.com'
            ],
            'canonical_link' => $user->locale_url,
            'voter' => $user,
            'weekly_leaderboard' => $weekly_leaderboard,
            'monthly_leaderboard' => $monthly_leaderboard,
            'yearly_leaderboard' => $yearly_leaderboard,
            'games' => $games
        ]);
    }
}
