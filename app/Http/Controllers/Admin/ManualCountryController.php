<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Facades\DataPull;
use App\ManualCountry;

class ManualCountryController extends AdminController
{
    public function toggleActive($id)
    {
        $country = ManualCountry::withTrashed()->findOrFail($id);
        if ($country->trashed()) {
            $country->restore();
        } else {
            $country->delete();
        }

        return $country;
    }

    public function index(Request $request)
    {
        $name = $request->input('name');
        $inactive = filter_var($request->input('inactive', false), FILTER_VALIDATE_BOOLEAN);
        $countries = ManualCountry::where(function ($query) use ($name) {
            $query
                ->orWhere('name', 'LIKE', "%$name%")
                ->orWhere('slug', 'LIKE', "%$name%")
                ->orWhere('name_es', 'LIKE', "%$name%")
                ->orWhere('slug_es', 'LIKE', "%$name%");
        });
        if ($inactive) {
            $countries->onlyTrashed();
        }

        return $countries->orderBy('name', 'ASC')->get();
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
        $data['display_order'] = ManualCountry::getNextDisplayOrder();
        $sport = ManualCountry::create($data);

        return $sport;
    }

    public function update(Request $request, $id)
    {
        $sport = ManualCountry::withTrashed()->findOrFail($id);
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
        ManualCountry::withTrashed()
            ->where('display_order', $current_order)
            ->update(['display_order' => 0]);
        if ($down) {
            ManualCountry::withTrashed()
                ->where('display_order', '>', $current_order)
                ->where('display_order', '<=', $desired_order)
                ->update(['display_order' => \DB::raw('display_order - 1')]);
        } else {
            ManualCountry::withTrashed()
                ->where('display_order', '>=', $desired_order)
                ->where('display_order', '<', $current_order)
                ->update(['display_order' => \DB::raw('display_order + 1')]);
        }
        ManualCountry::withTrashed()
            ->where('display_order', 0)
            ->update(['display_order' => $desired_order]);

        return [];
    }

    public function destroy($id)
    {
        $sport = ManualCountry::withTrashed()->findOrFail($id);
        $sport->forceDelete();

        return $sport;
    }
}
