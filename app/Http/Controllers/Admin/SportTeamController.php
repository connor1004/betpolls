<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Facades\DataPull;
use App\Team;
use App\LeagueTeam;

class SportTeamController extends AdminController
{
    public function pull(Request $request)
    {
        DataPull::pullLeagues();
    }

    public function toggleActive($id)
    {
        $team = Team::withTrashed()->findOrFail($id);
        if ($team->trashed()) {
            $team->restore();
        } else {
            $team->delete();
            LeagueTeam::where('team_id', $id)->delete();
        }

        return $team;
    }

    public function index(Request $request)
    {
        $sport_category_id = $request->input('sport_category_id', 0);
        $sport_area_id = $request->input('sport_area_id', 0);
        $is_player = $request->input('is_player', 0);
        $name = $request->input('name', '');

        if (empty($sport_area_id)) {
            $sport_area_id = 0;
        }
        $inactive = filter_var($request->input('inactive', false), FILTER_VALIDATE_BOOLEAN);
        $teams = Team::with(['sport_category', 'sport_area'])
            ->where(['sport_category_id' => $sport_category_id, 'sport_area_id' => $sport_area_id, 'is_player' => $is_player]);
        if ($name) {
            $teams = $teams->where(function ($query) use ($name) {
                $query
                    ->orWhere('name', 'LIKE', "%$name%")
                    ->orWhere('slug', 'LIKE', "%$name%")
                    ->orWhere('name_es', 'LIKE', "%$name%")
                    ->orWhere('slug_es', 'LIKE', "%$name%");
            });
        }
        if ($inactive) {
            $teams->onlyTrashed();
        }

        return $teams->orderBy('name', 'ASC')->paginate(100);
    }

    public function all(Request $request)
    {
        $sport_category_id = $request->input('sport_category_id', 0);
        $sport_area_id = $request->input('sport_area_id', 0);
        if (empty($sport_area_id)) {
            $sport_area_id = 0;
        }
        $inactive = filter_var($request->input('inactive', false), FILTER_VALIDATE_BOOLEAN);
        $teams = Team::with(['sport_category', 'sport_area'])
            ->where(['sport_category_id' => $sport_category_id, 'sport_area_id' => $sport_area_id]);

        if ($inactive) {
            $teams->onlyTrashed();
        }

        return $teams->orderBy('name', 'ASC')->get();
    }

    public function search(Request $request)
    {
        $sport_category_id = $request->input('sport_category_id', 0);
        $name = $request->input('name', '');

        if (empty($name)) {
            return [];
        }

        $teams = Team::where('sport_category_id', $sport_category_id)
            ->where('name', 'LIKE', "{$name}%");

        return $teams->orderBy('name', 'ASC')->get();
    }

    public function show($id)
    {
        $team = Team::findOrFail($id);

        return $team;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'sport_category_id' => 'required',
            'ref_id' => 'required',
            'sport_area_id' => 'required',
            'logo' => 'required',
            'name' => 'required',
            'name_es' => 'required',
            'short_name' => 'required',
            'short_name_es' => 'required',
        ]);
        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = str_slug($data['name']);
        }
        $team = Team::create($data);

        return $team;
    }

    public function update(Request $request, $id)
    {
        $team = Team::withTrashed()->findOrFail($id);
        $this->validate($request, [
            'ref_id' => 'required',
            'logo' => 'required',
            'name' => 'required',
            'name_es' => 'required',
            'short_name' => 'required',
            'short_name_es' => 'required',
        ]);
        $data = $request->all();
        $team->fill($data);
        $team->save();

        return $team;
    }

    public function destroy($id)
    {
        $team = Team::withTrashed()->findOrFail($id);
        $deleted = $team->forceDelete();
        LeagueTeam::where('team_id', $id)->delete();
        return $team;
    }
}
