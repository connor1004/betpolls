<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Facades\DataPull;
use App\League;

class SportLeagueController extends AdminController
{
    public function pull(Request $request)
    {
        DataPull::pullLeagues();
    }

    public function toggleActive($id)
    {
        $league = League::withTrashed()->findOrFail($id);
        if ($league->trashed()) {
            $league->restore();
        } else {
            $league->delete();
        }

        return $league;
    }

    public function index(Request $request)
    {
        $sport_category_id = $request->input('sport_category_id', 0);
        $inactive = filter_var($request->input('inactive', false), FILTER_VALIDATE_BOOLEAN);
        $leagues = League::with(['sport_category', 'sport_area', 'bet_types'])->where('sport_category_id', $sport_category_id);
        if ($inactive) {
            $leagues->onlyTrashed();
        }

        return $leagues->orderBy('display_order', 'ASC')->get();
    }

    public function all(Request $request)
    {
        $leagues = League::with(['sport_category'])->get()
            ->sortBy(function ($item1) {
                return (empty($item1->sport_category) ? 100 : $item1->sport_category->display_order) * 100000 + $item1->display_order;
            })->values();

        return $leagues;
    }

    public function show($id)
    {
        $league = League::findOrFail($id);

        return $league;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'sport_category_id' => 'required',
            'logo' => 'required',
            'name' => 'required',
            'name_es' => 'required'
        ]);
        
        $data = $request->all();
        if (empty($data['slug'])) {
            if (!empty($data['name'])) {
                $data['slug'] = str_slug($data['name']);
            }
        }
        if (empty($data['slug_es'])) {
            if (!empty($data['name_es'])) {
                $data['slug_es'] = str_slug($data['name_es']);
            }
        }
        $data['display_order'] = League::getNextDisplayOrder();
        $league = League::create($data);

        return $league;
    }

    public function update(Request $request, $id)
    {
        $league = League::withTrashed()->findOrFail($id);
        $this->validate($request, [
            'name' => 'required',
            'name_es' => 'required',
        ]);
        
        $data = $request->all();
        if (empty($data['slug'])) {
            if (!empty($data['name'])) {
                $data['slug'] = str_slug($data['name']);
            }
        }
        if (empty($data['slug_es'])) {
            if (!empty($data['name_es'])) {
                $data['slug_es'] = str_slug($data['name_es']);
            }
        }
        $league->fill($data);
        $league->save();

        return $league;
    }

    public function reorder(Request $request, $sport_category_id, $current_order, $desired_order)
    {
        $down = $desired_order > $current_order ? true : false;
        League::where('display_order', $current_order)
            ->where('sport_category_id', $sport_category_id)
            ->update(['display_order' => 0]);
        if ($down) {
            League::where('display_order', '>', $current_order)
                ->where('display_order', '<=', $desired_order)
                ->where('sport_category_id', $sport_category_id)
                ->update(['display_order' => \DB::raw('display_order - 1')]);
        } else {
            League::where('display_order', '>=', $desired_order)
                ->where('display_order', '<', $current_order)
                ->where('sport_category_id', $sport_category_id)
                ->update(['display_order' => \DB::raw('display_order + 1')]);
        }
        League::where('display_order', 0)
            ->where('sport_category_id', $sport_category_id)
            ->update(['display_order' => $desired_order]);

        return [];
    }

    public function attachBetTypes(Request $request, $id)
    {
        $league = League::withTrashed()->findOrFail($id);
        $league->bet_types()->detach();
        $bet_types = $request->input('bet_type_ids', []);
        if (count($bet_types) > 0) {
            $league->bet_types()->attach($bet_types);
        }

        return $league->bet_types;
    }

    public function destroy($id)
    {
        $league = League::withTrashed()->findOrFail($id);
        $deleted = $league->forceDelete();

        return $league;
    }
}
