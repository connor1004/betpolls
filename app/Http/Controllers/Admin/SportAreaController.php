<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Facades\DataPull;
use App\SportArea;

class SportAreaController extends AdminController
{
    public function getNextDisplayOrder()
    {
        $displayOrder = SportArea::withTrashed()->max('display_order') + 1;

        return $displayOrder;
    }

    public function pull(Request $request)
    {
        DataPull::pullSportArea();
    }

    public function toggleActive($id)
    {
        $sport = SportArea::withTrashed()->findOrFail($id);
        if ($sport->trashed()) {
            $sport->restore();
        } else {
            $sport->delete();
        }

        return $sport;
    }

    public function index(Request $request)
    {
        $name = $request->input('name');
        $inactive = filter_var($request->input('inactive', false), FILTER_VALIDATE_BOOLEAN);
        $sports = SportArea::where('name', 'LIKE', "%$name%");
        if ($inactive) {
            $sports->onlyTrashed();
        }

        return $sports->orderBy('display_order', 'ASC')->get();
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
        $data['display_order'] = $this->getNextDisplayOrder();
        $sport = SportArea::create($data);

        return $sport;
    }

    public function update(Request $request, $id)
    {
        $sport = SportArea::withTrashed()->findOrFail($id);
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
        SportArea::withTrashed()
            ->where('display_order', $current_order)
            ->update(['display_order' => 0]);
        if ($down) {
            SportArea::withTrashed()
                ->where('display_order', '>', $current_order)
                ->where('display_order', '<=', $desired_order)
                ->update(['display_order' => \DB::raw('display_order - 1')]);
        } else {
            SportArea::withTrashed()
                ->where('display_order', '>=', $desired_order)
                ->where('display_order', '<', $current_order)
                ->update(['display_order' => \DB::raw('display_order + 1')]);
        }
        SportArea::withTrashed()
            ->where('display_order', 0)
            ->update(['display_order' => $desired_order]);

        return [];
    }

    public function destroy($id)
    {
        $sport = SportArea::withTrashed()->findOrFail($id);
        $sport->forceDelete();

        return $sport;
    }
}
