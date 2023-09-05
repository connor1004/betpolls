<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Hash;

use App\Leaderboard;
use App\Game;
use App\GameBetType;
use App\League;
use App\Menu;
use App\User;
use App\Vote;
use App\ManualFutureVote;
use App\ManualEventVote;
use App\Facades\Geoip;

use DB;

class DataHelper
{
    public function getLeaderBoards($params = null)
    {
        $start_at = Carbon::now()->setTimezone('America/New_York')->startOfDay()->subDays(7)->setTimezone('UTC')->format('Y-m-d H:i:s');
        $end_at = Carbon::now()->setTimezone('America/New_York')->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');

        $leaderboardsA = null;
        $leaderboardsM = null;
        
        $leaderboardsA = Vote::join('games', 'votes.game_id', '=', 'games.id')
                ->join('leagues', 'games.league_id', '=', 'leagues.id')
                ->select('votes.user_id', DB::raw('SUM(score) AS score'))
                ->whereBetween('games.start_at', [$start_at, $end_at])
                ->whereNotNull('games.calculated_at')
                ->whereNotNull('games.calculating_at')
                ->whereHas('user', function ($query) {
                    $query->where('role', '!=', User::$ROLE_UNKNOWN);
                });
        $leaderboardsA = $leaderboardsA->groupBy('votes.user_id');

        $leaderboardsF = ManualFutureVote::join('manual_poll_pages', 'manual_future_votes.page_id', '=', 'manual_poll_pages.id')
                ->select('user_id', 'score')
                ->whereBetween('manual_poll_pages.start_at', [$start_at, $end_at])
                ->whereNotNull('manual_poll_pages.calculated_at')
                ->whereNotNull('manual_poll_pages.calculating_at')
                ->whereHas('user', function ($query) {
                    $query->where('role', '!=', User::$ROLE_UNKNOWN);
                });
        
        $leaderboardsE = ManualEventVote::join('manual_poll_pages', 'manual_event_votes.page_id', '=', 'manual_poll_pages.id')
                ->select('user_id', 'score')
                ->whereBetween('manual_poll_pages.start_at', [$start_at, $end_at])
                ->whereNotNull('manual_poll_pages.calculated_at')
                ->whereNotNull('manual_poll_pages.calculating_at')
                ->whereHas('user', function ($query) {
                    $query->where('role', '!=', User::$ROLE_UNKNOWN);
                });

        $leaderboardsM = $leaderboardsF->unionAll($leaderboardsE);
        $leaderboardsM = DB::query()->fromSub($leaderboardsM, 'f_e')
                ->select('user_id', DB::raw('SUM(score) AS score'))
                ->groupBy('user_id');

        $leaderboards = $leaderboardsA->unionAll($leaderboardsM);
        $leaderboards = DB::query()->fromSub($leaderboards, 'a_m')
                ->select('user_id', DB::raw('SUM(score) as score'))
                ->groupBy('user_id');
        $leaderboards = $leaderboards->orderBy('score', 'DESC')->orderBy('user_id')->limit(10);

        return $leaderboards->get();

        // $geoip = Geoip::getGeoip();
        // $timezone = $geoip ? $geoip->time_zone : 'UTC';
        // $start_at = (new Carbon(null, $timezone))->startOfDay()->subDays(7)->setTimezone('UTC')->format('Y-m-d H:i:s');
        // $end_at = (new Carbon(null, $timezone))->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');

        // $start_at = Carbon::now()->setTimezone('America/New_York')->startOfDay()->subDays(7)->setTimezone('UTC')->format('Y-m-d H:i:s');
        // $end_at = Carbon::now()->setTimezone('America/New_York')->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        // $list = Vote::join('games', 'votes.game_id', '=', 'games.id')
        //     ->select('votes.user_id', DB::raw('SUM(score) AS score'))
        //     ->whereHas('user', function ($query) {
        //         $query->where('role', '!=', User::$ROLE_UNKNOWN);
        //     })
        //     ->whereBetween('games.start_at', [$start_at, $end_at])
        //     ->groupBy('votes.user_id')
        //     ->orderBy('score', 'DESC')
        //     ->limit(10)->get();
        // return $list;
    }

    public function getGames($params = null)
    {
        // $geoip = Geoip::getGeoip();
        // $timezone = $geoip ? $geoip->time_zone : 'UTC';

        // $start_at = (new Carbon(null, $timezone))->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        // $end_at = (new Carbon(null, $timezone))->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $start_at = (new Carbon())->setTimezone('America/New_York')->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $end_at = (new Carbon())->setTimezone('America/New_York')->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $gamesBuilder = Game::whereBetween('start_at', [$start_at, $end_at]);

        if ($params) {
            if (isset($params->league_id) && !empty($params->league_id)) {
                $gamesBuilder->where('league_id', $params->league_id);
            }
        }

        $games = $gamesBuilder->orderBy(DB::raw('RAND()'))->limit(5)->get();
        return $games->sortBy(function ($game) {
            return $game->start_at;
        });
    }

    public function getLeagues()
    {
        return League::whereHas('today_games')->orderBy('display_order')->get();
    }

    public function getTopPicks($params = null)
    {
        // $geoip = Geoip::getGeoip();
        // $timezone = $geoip ? $geoip->time_zone : 'UTC';
        // $start_at = (new Carbon(null, $timezone))->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        // $end_at = (new Carbon(null, $timezone))->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $start_at = (new Carbon())->setTimezone('America/New_York')->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $end_at = (new Carbon())->setTimezone('America/New_York')->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $votes = DB::select("
            SELECT COUNT(vote_case) as vote_case_count, votes.game_bet_type_id, votes.vote_case
                FROM votes, games
                WHERE votes.game_id=games.id AND games.start_at BETWEEN :start_at AND :end_at
                GROUP BY votes.game_bet_type_id, votes.vote_case
            ORDER BY vote_case_count DESC
            LIMIT 0, 5
        ", ['start_at' => $start_at, 'end_at' => $end_at]);
        foreach ($votes as &$vote) {
            $vote->game_bet_type = GameBetType::find($vote->game_bet_type_id);
        }
        return $votes;
    }

    public function getMenus($menu_type = 'header')
    {
        return Menu::where('parent_id', 0)->where('menu_type', $menu_type)->orderBy('display_order')->get();
    }

    public function getUser()
    {
        if (Auth::check()) {
            return Auth::user();
        }

        $ip = Request::ip();
        $user = User::where('username', $ip)->first();

        if ($user === null) {
            $password = (new Carbon())->getTimestamp();
            $user = User::create([
                'firstname' => $ip,
                'lastname' => $ip,
                'secondname' => $ip,
                'username' => $ip,
                'email' => "{$ip}@betpolls.com",
                'password' => Hash::make($password),
                'role' => User::$ROLE_UNKNOWN
            ]);
        }
        return $user;
    }
}
