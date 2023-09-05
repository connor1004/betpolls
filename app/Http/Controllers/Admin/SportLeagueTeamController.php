<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\LeagueTeam;

class SportLeagueTeamController extends AdminController
{
    public function index(Request $request, $league_id)
    {
        $league_teams = LeagueTeam::with(['league', 'team', 'league_division'])
            ->where('league_id', $league_id)->get()
            ->sortBy(function ($item) {
                if ($item->league_division) {
                    return sprintf('%08d%s', $item->league_division->display_order, $item->team->name);
                }

                return $item->team ? $item->team->name : '99999999';
            })
            ->values()->all();
        $teams = [];
        foreach ($league_teams as $league_team) {
            if ($league_team->team) {
                $teams[] = $league_team;
            }
        }

        return $teams;
    }

    public function store(Request $request, $league_id)
    {
        $this->validate($request, [
            'team_id' => 'required',
            'league_id' => 'required',
        ]);
        $data = $request->only([
            'league_id', 'league_division_id', 'team_id',
        ]);
        $league_team = LeagueTeam::where([
            'league_id' => $data['league_id'],
            'team_id' => $data['team_id'],
        ])->first();
        if (!$league_team) {
            $league_team = LeagueTeam::create($data);
        }

        return $league_team;
    }

    public function destroy($league_id, $team_id)
    {
        $league_team = LeagueTeam::where([
            'league_id' => $league_id,
            'team_id' => $team_id,
        ])->delete();

        return $league_team;
    }
}
