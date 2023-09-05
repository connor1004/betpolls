<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\LeagueDivision;

class SportLeagueDivisionController extends AdminController
{
    public function toggleActive($league_id, $id)
    {
        $leagues = LeagueDivision::withTrashed()->findOrFail($id);
        if ($league->trashed()) {
            $league->restore();
        } else {
            $league->delete();
        }

        return $league;
    }

    public function index(Request $request, $league_id)
    {
        $inactive = filter_var($request->input('inactive', false), FILTER_VALIDATE_BOOLEAN);
        $leagues = LeagueDivision::where('league_id', $league_id);
        if ($inactive) {
            $leagues->onlyTrashed();
        }

        return $leagues->orderBy('display_order', 'ASC')->get();
    }

    public function show($league_id, $id)
    {
        $league = LeagueDivision::findOrFail($id);

        return $league;
    }

    public function store(Request $request, $league_id, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'name_es' => 'required',
            'league_id' => 'required',
            'logo' => 'required',
        ]);
        $data = $request->only([
            'parent_id', 'name', 'name_es', 'logo', 'league_id',
        ]);
        $data['display_order'] = $this->getNextDisplayOrder();
        $league = LeagueDivision::create($data);

        return $league;
    }

    public function update(Request $request, $league_id, $id)
    {
        $league = LeagueDivision::withTrashed()->findOrFail($id);
        $this->validate($request, [
            'name' => 'required',
            'name_es' => 'required',
        ]);
        $data = $request->only([
            'parent_id', 'name', 'name_es', 'logo', 'league_id',
        ]);
        $league->fill($data);
        $league->save();

        return $league;
    }

    public function reorder(Request $request, $league_id, $current_order, $desired_order)
    {
        $down = $desired_order > $current_order ? true : false;
        LeagueDivision::where('display_order', $current_order)
            ->where('league_id', $league_id)
            ->update(['display_order' => 0]);
        if ($down) {
            LeagueDivision::where('display_order', '>', $current_order)
                ->where('display_order', '<=', $desired_order)
                ->where('league_id', $league_id)
                ->update(['display_order' => \DB::raw('display_order - 1')]);
        } else {
            LeagueDivision::where('display_order', '>=', $desired_order)
                ->where('display_order', '<', $current_order)
                ->where('league_id', $league_id)
                ->update(['display_order' => \DB::raw('display_order + 1')]);
        }
        LeagueDivision::where('display_order', 0)
            ->where('league_id', $league_id)
            ->update(['display_order' => $desired_order]);

        return [];
    }

    public function destroy($league_id, $id)
    {
        $sport = LeagueDivision::withTrashed()->findOrFail($id);
        $deleted = $sport->forceDelete();

        return $sport;
    }
}
