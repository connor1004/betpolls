<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Facades\Geoip;
use App\League;
use App\Game;
use Carbon\Carbon;
use App\Facades\Options;
use Log;

class LeagueController extends Controller
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

    public function getGames(Request $request, $league)
    {
        // $timezone = Geoip::getGeoip() ? Geoip::getGeoip()->time_zone : 'UTC';
        // $date = $request->get('start_at', (new Carbon)->setTimezone($timezone)->format('Y-m-d'));
        $date = $request->get('start_at', (new Carbon)->setTimezone('America/New_York')->format('Y-m-d'));
        
        // $start_at = Carbon::createFromFormat('Y-m-d', $date, $timezone)->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        // $end_at = Carbon::createFromFormat('Y-m-d', $date, $timezone)->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $start_at = Carbon::createFromFormat('Y-m-d', $date, 'America/New_York')->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $end_at = Carbon::createFromFormat('Y-m-d', $date, 'America/New_York')->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $games = Game::whereBetween('start_at', [$start_at, $end_at])
            ->where('league_id', $league->id)->orderBy('start_at')->get();
        
            
        if ($games->count() > 0 || $request->get('start_at')) {
            return [
                'games' => $games,
                'start_at' => $date
            ];
        }
        
        $game = Game::where('league_id', $league->id)->where('start_at', '>=', $start_at)->orderBy('start_at', 'ASC')->first();
        if ($game) {
            // $start_at = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone($timezone)->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            // $end_at = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone($timezone)->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            $start_at = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone('America/New_York')->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            $end_at = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone('America/New_York')->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            $games = Game::whereBetween('start_at', [$start_at, $end_at])
                ->where('league_id', $league->id)->orderBy('start_at')->get();
            return [
                'games' => $games,
                // 'start_at' => Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone($timezone)->format('Y-m-d')
                'start_at' => Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone('America/New_York')->format('Y-m-d')
            ];
        }

        $game = Game::where('league_id', $league->id)->where('start_at', '<', $start_at)->orderBy('start_at', 'DESC')->first();
        if ($game) {
            // $start_at = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone($timezone)->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            // $end_at = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone($timezone)->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            $start_at = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone('America/New_York')->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            $end_at = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone('America/New_York')->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            $games = Game::whereBetween('start_at', [$start_at, $end_at])
                ->where('league_id', $league->id)->orderBy('start_at')->get();
            return [
                'games' => $games,
                // 'start_at' => Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone($timezone)->format('Y-m-d')
                'start_at' => Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone('America/New_York')->format('Y-m-d')
            ];
        }

        return [
            'games' => collect([]),
            'start_at' => $date
        ];
    }

    public function index(Request $request, String $categorySlug, String $leagueSlug)
    {
        $locale = app('translator')->getLocale();
        $league = null;
        if ($locale === 'es') {
            $league = League::where('slug_es', $leagueSlug)->first();
        }
        if (!$league) {
            $league = League::where('slug', $leagueSlug)->firstOrFail();
        }
        
        $games = $this->getGames($request, $league);
        
        if ($request->input('ajax')) {
            return view('front.snippets.league-games', [
                'league' => $league,
                'games' => $games['games'],
                'start_at' => $games['start_at']
            ]);
        }
        $socials = Options::getSocialMediaLinkOption();
        return view('front.leagues', [
            'page' => $league,
            'league' => $league,
            'games' => $games['games'],
            'socials' => $socials,
            'start_at' => $games['start_at']
        ]);
    }
}
