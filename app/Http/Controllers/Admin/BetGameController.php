<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

use App\Facades\Geoip;
use App\Game;
use App\Facades\DataPull;
use App\Facades\Calculation;
use App\Facades\Evaluation;
use DB;
use Carbon\Carbon;

class BetGameController extends AdminController
{
    public function index(Request $request)
    {
        // $geoip = Geoip::getGeoip();
        // $timezone = $geoip ? $geoip->time_zone : 'UTC';

        // $timezone = Geoip::getGeoip() ? Geoip::getGeoip()->time_zone : 'UTC';
        $timezone = 'America/New_York';
        
        $date = $request->get('start_at', (new Carbon)->setTimezone($timezone)->format('Y-m-d'));
        $start_at = Carbon::createFromFormat('Y-m-d', $date, $timezone)->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');

        $date = $request->get('end_at', (new Carbon)->setTimezone($timezone)->format('Y-m-d'));
        $end_at = Carbon::createFromFormat('Y-m-d', $date, $timezone)->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');

        $league_id = $request->input('league_id');
        $inactive = filter_var($request->input('inactive', false), FILTER_VALIDATE_BOOLEAN);
        $games = Game::with(['home_team', 'away_team', 'game_bet_types'])
            ->where('league_id', $league_id)
            ->whereBetween('start_at', [$start_at, $end_at]);
        if ($inactive) {
            $games->onlyTrashed();
        }

        return $games->orderBy('start_at')->get();
    }

    public function importList(Request $request)
    {
        // $geoip = Geoip::getGeoip();
        // $timezone = $geoip ? $geoip->time_zone : 'UTC';

        // $timezone = Geoip::getGeoip() ? Geoip::getGeoip()->time_zone : 'UTC';
        $timezone = 'America/New_York';

        $date = $request->get('date_start', (new Carbon)->setTimezone($timezone)->format('Y-m-d'));
        $date_start = Carbon::createFromFormat('Y-m-d', $date, $timezone)->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');

        $date = $request->get('date_end', (new Carbon)->setTimezone($timezone)->format('Y-m-d'));
        $date_end = Carbon::createFromFormat('Y-m-d', $date, $timezone)->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');

        $league_id = $request->input('league_id');
        $params = [
            'date_start' => $date_start,
            'date_end' => $date_end,
            'league_id' => $league_id
        ];

        return DataPull::games($params);
    }

    public function import(Request $request)
    {
        $data = $request->input('data', "[]");
        $data = json_decode($data, true);
        $league_id = $request->input('league_id', 0);
        DataPull::importGames($league_id, $data);

        return [
            'error' => null,
        ];
    }

    public function importAll(Request $request)
    {
        $league_id = $request->input('league_id', 0);
    }

    public function pull($id)
    {
        $game = Game::findOrFail($id);
        $params = ['game_id' => $game->ref_id];
        $results = DataPull::games($params);
        $game_status = Game::$STATUS_NOT_STARTED;

        switch ($results->sport_game_status) {
            case Game::$IMPORT_STATUS_STARTED:
                $game_status = Game::$STATUS_STARTED;
                break;
            case Game::$IMPORT_STATUS_CANCELLED:
            case Game::$IMPORT_STATUS_SUSPENDED:
                $game_status = Game::$STATUS_POSTPONED;
                break;
            case Game::$IMPORT_STATUS_ENDED:
                $game_status = Game::$STATUS_ENDED;
                break;
            default:
                break;
        }

        $data = [
            'home_team_score' => $results->sport_game_live_info->hometeam->totalscore,
            'away_team_score' => $results->sport_game_live_info->awayteam->totalscore,
            'game_info' => $results->sport_game_live_info,
            'status' => $game_status
        ];

        if ($results->sport_game_status === Game::$IMPORT_SATUS_NOT_STARTED || $results->sport_game_status === Game::$IMPORT_STATUS_CANCELLED || $results->sport_game_status === Game::$IMPORT_STATUS_SUSPENDED) {
            $data['game_general_info'] = $results->sport_game_general_info;
        }
        $game->update($data);
        if ($game_status === Game::$STATUS_ENDED) {
            if (empty($game->calculating_at) && empty($game->calculated_at)) {
                Artisan::call('game:calculate', [
                    'game' => $game->id
                ]);
            }
        }
        $game = Game::findOrFail($id);
        return $game;
    }

    public function toggleActive($id)
    {
        $game = Game::withTrashed()->findOrFail($id);
        if ($game->trashed()) {
            $game->restore();
        } else {
            $game->delete();
        }

        return $game;
    }

    public function toggleActiveGames(Request $request)
    {
        $games = Game::withTrashed()->whereIn('id', $request->input('games'))->get();
        foreach ($games as $game) {
            if ($game->trashed()) {
                $game->restore();
            } else {
                $game->delete();
            }
        }
        return $games;
    }

    public function show($id)
    {
        $game = Game::withTrashed()->findOrFail($id);
        $game->createBetTypes();
        $game->load(['game_bet_types.bet_type', 'league.bet_types', 'home_team', 'away_team']);
        return $game;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'start_at' => 'required|date_format:Y-m-d H:i:s',
            'league_id' => 'required|numeric',
            'home_team_id' => 'required|numeric',
            'away_team_id' => 'required|numeric',
        ]);
        $data = $request->all();
        $game = Game::create($data);
        $game = Game::with(['home_team', 'away_team'])->find($game->id);

        return $game;
    }

    public function update(Request $request, $id)
    {
        $game = Game::withTrashed()->findOrFail($id);
        $original_start_at = null;
        if ($game->calculated) {
            Calculation::cancelGameCalculation($game);
            $original_start_at = $game->start_at;
        }
        $data = $request->all();
        $game->fill($data);
        $modified_start_at = null;
        if ($game->status === Game::$STATUS_ENDED) {
            $modified_start_at = $game->start_at;
            Calculation::calculateGameScore($game);
        }
        $game->save();

        Evaluation::reevaluate($original_start_at, $modified_start_at);

        $game->with(['home_team', 'away_team']);

        return $game;
    }

    public function destroy($id)
    {
        $game = Game::withTrashed()->findOrFail($id);
        $game->forceDelete();

        return $game;
    }

    public function destroyGames(Request $request)
    {
        $games = Game::withTrashed()->whereIn('id', $request->input('games'))->get();
        foreach ($games as $game) {
            $game->forceDelete();
        }
        return $games;
    }
}
