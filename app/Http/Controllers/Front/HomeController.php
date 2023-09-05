<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Facades\Options;
use App\Facades\Geoip;
use App\League;
use App\Game;
use App\GameBetType;
use App\Post;
use Carbon\Carbon;
use DB;

class HomeController extends Controller
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
        $options = Options::getOption('home_leagues', []);
        $leagues = League::whereIn('id', $options)->get();
        $date = $request->get('start_at', (new Carbon())->setTimezone('America/New_York')->format('Y-m-d'));

        $start_time = Carbon::createFromFormat('Y-m-d H:i:s', $date.' 00:00:00', 'America/New_York')
                ->setTimezone('UTC')->format('Y-m-d H:i:s');

        $end_time = Carbon::createFromFormat('Y-m-d H:i:s', $date.' 00:00:00', 'America/New_York')
                ->addDays(1)->setTimezone('UTC')->format('Y-m-d H:i:s');

        // $games = Game::where(DB::raw("DATE_FORMAT(start_at, '%Y-%m-%d')"), $date)
        //     ->whereIn('league_id', $options)->get();
        $games = Game::where('start_at', '>=', $start_time)->where('start_at', '<', $end_time)
            ->whereIn('league_id', $options)->get();

        $leagues_groups = [];
        foreach ($leagues as $league) {
            $leagues_group = (object)[
                'league' => $league,
                'games' => []
            ];
            foreach ($games as $game) {
                if ($game->league_id === $league->id) {
                    $leagues_group->games[] = $game;
                }
            }

            if (count($leagues_group->games) > 0) {
                $leagues_groups[] = $leagues_group;
            }
        }

        if ($request->input('ajax')) {
            return view('front.snippets.leagues-groups', [
                'leagues_groups' => $leagues_groups,
            ]);
        }

        $page = Post::where('slug', 'home')->where('post_type', Post::$POST_TYPE_HOME)->first();
        $about = Post::where('slug', 'about')->where('post_type', Post::$POST_TYPE_HOME)->first();
        $settings = Options::getSettingsOption();

        return view('front.index', [
            'page' => $page,
            'homepage' => $about,
            'leagues_groups' => $leagues_groups
        ]);
    }

    public function toppick() {
        $start_at = (new Carbon())->setTimezone('America/New_York')->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $end_at = (new Carbon())->setTimezone('America/New_York')->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $votes = DB::select("
            SELECT COUNT(vote_case) as vote_case_count, votes.game_bet_type_id, votes.vote_case
                FROM votes, games
                WHERE votes.game_id=games.id AND games.start_at BETWEEN :start_at AND :end_at
                GROUP BY votes.game_bet_type_id, votes.vote_case
            ORDER BY vote_case_count DESC
            LIMIT 0, 25
        ", ['start_at' => $start_at, 'end_at' => $end_at]);
        foreach ($votes as &$vote) {
            $vote->game_bet_type = GameBetType::find($vote->game_bet_type_id);
        }

        $page = Post::where('slug', 'top-picks')->where('post_type', Post::$POST_TYPE_PAGE)->first();
        
        return view('front.toppick', [
            'page' => $page,
            'picks' => $votes
        ]);
    }

    public function chat() {
        $page = Post::where('slug', 'chat')->where('post_type', Post::$POST_TYPE_PAGE)->first();
        return view('front.chat', [
            'page' => $page,
        ]);
    }

    public function test()
    {
        $str = str_random(16);
        return $str . ", " . Hash::make($str);
        // return str_random(16);
    }
}
