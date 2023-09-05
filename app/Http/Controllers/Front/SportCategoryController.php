<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Facades\Geoip;
use App\League;
use App\Game;
use App\SportCategory;
use Carbon\Carbon;
use DB;

class SportCategoryController extends Controller
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

    public function index(Request $request, String $slug)
    {
        $locale = app('translator')->getLocale();
        $sport_category = null;
        if ($locale === 'es') {
            $sport_category = SportCategory::where('slug_es', $slug)->first();
        }
        if (!$sport_category) {
            $sport_category = SportCategory::where('slug', $slug)->firstOrFail();
        }

        $leagues_groups = $this->getLeagueGroups($request, $sport_category);
        
        if ($request->input('ajax')) {
            return view('front.snippets.leagues-groups', [
                'leagues_groups' => $leagues_groups['groups'],
                'start_at' => $leagues_groups['start_at']
            ]);
        }

        return view('front.sport-categories', [
            'page' => (object)[
                'title' => $sport_category->title,
                'meta_description' => $sport_category->meta_description,
                'meta_keywords' => $sport_category->meta_keywords,
                'page_class' => 'page-index'
            ],
            'sport_category' => $sport_category,
            'leagues_groups' => $leagues_groups['groups'],
            'start_at' => $leagues_groups['start_at']
        ]);
    }

    public function getLeagueGroups($request, $category) {
        $leagues = League::where('sport_category_id', $category->id)->orderBy('display_order')->get();
        
        $league_ids = [];

        foreach ($leagues as $league) {
            $league_ids[] = $league->id;
        }

        $games = $this->getGames($request, $league_ids);

        $leagues_groups = [];
        foreach ($leagues as $league) {
            $leagues_group = (object)[
                'league' => $league,
                'games' => []
            ];
            foreach ($games['games'] as $game) {
                if ($game->league_id === $league->id) {
                    $leagues_group->games[] = $game;
                }
            }

            if (count($leagues_group->games) > 0) {
                $leagues_groups[] = $leagues_group;
            }
        }

        return [
            'groups' => $leagues_groups,
            'start_at' => $games['start_at']
        ];
    }

    public function getGames($request, $league_ids) {
        // $timezone = Geoip::getGeoip() ? Geoip::getGeoip()->time_zone : 'UTC';
        // $date = $request->get('start_at', (new Carbon)->setTimezone($timezone)->format('Y-m-d'));
        // $start_at = Carbon::createFromFormat('Y-m-d', $date, $timezone)->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        // $end_at = Carbon::createFromFormat('Y-m-d', $date, $timezone)->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $date = $request->get('start_at', (new Carbon)->setTimezone('America/New_York')->format('Y-m-d'));
        $start_at = Carbon::createFromFormat('Y-m-d', $date, 'America/New_York')->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $end_at = Carbon::createFromFormat('Y-m-d', $date, 'America/New_York')->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');

        $games = Game::whereBetween('start_at', [$start_at, $end_at])
            ->whereIn('league_id', $league_ids)->orderBy('start_at')->get();
        
        if ($games->count() > 0 || $request->get('start_at')) {
            return [
                'games' => $games,
                'start_at' => $date
            ];
        }
        
        $game = Game::whereIn('league_id', $league_ids)->where('start_at', '>=', $start_at)->orderBy('start_at', 'ASC')->first();
        if ($game) {
            // $start_at = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone($timezone)->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            // $end_at = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone($timezone)->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            $start_at = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone('America/New_York')->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            $end_at = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone('America/New_York')->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            
            $games = Game::whereBetween('start_at', [$start_at, $end_at])
                ->whereIn('league_id', $league_ids)->orderBy('start_at')->get();
            return [
                'games' => $games,
                'start_at' => Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone('America/New_York')->format('Y-m-d')
                // 'start_at' => Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone($timezone)->format('Y-m-d')
            ];
        }

        $game = Game::whereIn('league_id', $league_ids)->where('start_at', '<', $start_at)->orderBy('start_at', 'DESC')->first();
        if ($game) {
            // $start_at = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone($timezone)->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            // $end_at = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone($timezone)->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            $start_at = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone('America/New_York')->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            $end_at = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone('America/New_York')->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            $games = Game::whereBetween('start_at', [$start_at, $end_at])
                ->whereIn('league_id', $league_ids)->orderBy('start_at')->get();
            return [
                'games' => $games,
                'start_at' => Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone('America/New_York')->format('Y-m-d')
                // 'start_at' => Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at)->setTimezone($timezone)->format('Y-m-d')
            ];
        }

        return [
            'games' => collect([]),
            'start_at' => $date
        ];
    }
}
