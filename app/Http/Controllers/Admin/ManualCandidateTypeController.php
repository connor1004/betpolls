<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Facades\DataPull;
use App\ManualCandidateType;

class ManualCandidateTypeController extends AdminController
{
    public function toggleActive($id)
    {
        $type = ManualCandidateType::withTrashed()->findOrFail($id);
        if ($type->trashed()) {
            $type->restore();
        } else {
            $type->delete();
        }

        return $type;
    }

    public function index(Request $request)
    {
        $name = $request->input('name');
        $inactive = filter_var($request->input('inactive', false), FILTER_VALIDATE_BOOLEAN);
        $types = ManualCandidateType::where(function ($query) use ($name) {
            $query
                ->orWhere('name', 'LIKE', "%$name%")
                ->orWhere('slug', 'LIKE', "%$name%")
                ->orWhere('name_es', 'LIKE', "%$name%")
                ->orWhere('slug_es', 'LIKE', "%$name%");
        });
        if ($inactive) {
            $types->onlyTrashed();
        }

        return $types->orderBy('name', 'ASC')->get();
    }

    public function show($id)
    {
        $type = ManualCandidateType::findOrFail($id);

        return $type;
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
        $data['display_order'] = ManualCandidateType::getNextDisplayOrder();
        $type = ManualCandidateType::create($data);

        return $type;
    }

    public function update(Request $request, $id)
    {
        $type = ManualCandidateType::withTrashed()->findOrFail($id);
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
        $type->fill($data);
        $type->save();

        return $type;
    }

    public function reorder(Request $request, $current_order, $desired_order)
    {
        $down = $desired_order > $current_order ? true : false;
        ManualCandidateType::withTrashed()
            ->where('display_order', $current_order)
            ->update(['display_order' => 0]);
        if ($down) {
            ManualCandidateType::withTrashed()
                ->where('display_order', '>', $current_order)
                ->where('display_order', '<=', $desired_order)
                ->update(['display_order' => \DB::raw('display_order - 1')]);
        } else {
            ManualCandidateType::withTrashed()
                ->where('display_order', '>=', $desired_order)
                ->where('display_order', '<', $current_order)
                ->update(['display_order' => \DB::raw('display_order + 1')]);
        }
        ManualCandidateType::withTrashed()
            ->where('display_order', 0)
            ->update(['display_order' => $desired_order]);

        return [];
    }

    public function destroy($id)
    {
        $type = ManualCandidateType::withTrashed()->findOrFail($id);
        $deleted = $type->forceDelete();

        return $type;
    }
}
