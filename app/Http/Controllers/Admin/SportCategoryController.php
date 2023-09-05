<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Facades\DataPull;
use App\SportCategory;
use App\League;

class SportCategoryController extends AdminController
{
    public function pull(Request $request)
    {
        DataPull::pullSportCategory();
    }

    public function toggleActive($id)
    {
        $sport = SportCategory::withTrashed()->findOrFail($id);
        $leagues = League::withTrashed()->where('sport_category_id', $id)->get();
        if ($sport->trashed()) {
            $sport->restore();
            foreach ($leagues as $league) {
                $league->restore();
            }
        } else {
            $sport->delete();
            foreach ($leagues as $league) {
                $league->delete();
            }
        }

        return $sport;
    }

    public function index(Request $request)
    {
        $name = $request->input('name');
        $inactive = filter_var($request->input('inactive', false), FILTER_VALIDATE_BOOLEAN);
        $sports = SportCategory::where(function ($query) use ($name) {
            $query
                ->orWhere('name', 'LIKE', "%$name%")
                ->orWhere('slug', 'LIKE', "%$name%")
                ->orWhere('name_es', 'LIKE', "%$name%")
                ->orWhere('slug_es', 'LIKE', "%$name%");
        });
        if ($inactive) {
            $sports->onlyTrashed();
        }

        return $sports->orderBy('display_order', 'ASC')->get();
    }

    public function show($id)
    {
        $sport = SportCategory::findOrFail($id);

        return $sport;
    }

    public function store(Request $request)
    {
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
        $data['display_order'] = SportCategory::getNextDisplayOrder();
        $sport = SportCategory::create($data);

        return $sport;
    }

    public function update(Request $request, $id)
    {
        $sport = SportCategory::withTrashed()->findOrFail($id);
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
        $sport->fill($data);
        $sport->save();

        return $sport;
    }

    public function reorder(Request $request, $current_order, $desired_order)
    {
        $down = $desired_order > $current_order ? true : false;
        SportCategory::withTrashed()
            ->where('display_order', $current_order)
            ->update(['display_order' => 0]);
        if ($down) {
            SportCategory::withTrashed()
                ->where('display_order', '>', $current_order)
                ->where('display_order', '<=', $desired_order)
                ->update(['display_order' => \DB::raw('display_order - 1')]);
        } else {
            SportCategory::withTrashed()
                ->where('display_order', '>=', $desired_order)
                ->where('display_order', '<', $current_order)
                ->update(['display_order' => \DB::raw('display_order + 1')]);
        }
        SportCategory::withTrashed()
            ->where('display_order', 0)
            ->update(['display_order' => $desired_order]);

        return [];
    }

    public function destroy($id)
    {
        $sport = SportCategory::withTrashed()->findOrFail($id);
        $deleted = $sport->forceDelete();
        $leagues = League::withTrashed()->where('sport_category_id', $id)->get();
        foreach ($leagues as $league) {
            $league->forceDelete();
        }

        return $sport;
    }
}
