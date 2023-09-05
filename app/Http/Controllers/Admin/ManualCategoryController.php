<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Facades\DataPull;
use App\ManualCategory;
use App\ManualSubcategory;

class ManualCategoryController extends AdminController
{
    public function toggleActive($id)
    {
        $category = ManualCategory::withTrashed()->findOrFail($id);
        $subcategories = ManualSubcategory::withTrashed()->where('category_id', $id)->get();
        if ($category->trashed()) {
            $category->restore();
            foreach ($subcategories as $subcategory) {
                $subcategory->restore();
            }
        } else {
            $category->delete();
            foreach ($subcategories as $subcategory) {
                $subcategory->delete();
            }
        }

        return $category;
    }

    public function index(Request $request)
    {
        $name = $request->input('name');
        $inactive = filter_var($request->input('inactive', false), FILTER_VALIDATE_BOOLEAN);
        $categories = ManualCategory::where(function ($query) use ($name) {
            $query
                ->orWhere('name', 'LIKE', "%$name%")
                ->orWhere('slug', 'LIKE', "%$name%")
                ->orWhere('name_es', 'LIKE', "%$name%")
                ->orWhere('slug_es', 'LIKE', "%$name%");
        });
        if ($inactive) {
            $categories->onlyTrashed();
        }

        return $categories->orderBy('name', 'ASC')->get();
    }

    public function show($id)
    {
        $category = ManualCategory::findOrFail($id);

        return $category;
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
        $data['display_order'] = ManualCategory::getNextDisplayOrder();
        $category = ManualCategory::create($data);

        return $category;
    }

    public function update(Request $request, $id)
    {
        $category = ManualCategory::withTrashed()->findOrFail($id);
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
        $category->fill($data);
        $category->save();

        return $category;
    }

    public function reorder(Request $request, $current_order, $desired_order)
    {
        $down = $desired_order > $current_order ? true : false;
        ManualCategory::withTrashed()
            ->where('display_order', $current_order)
            ->update(['display_order' => 0]);
        if ($down) {
            ManualCategory::withTrashed()
                ->where('display_order', '>', $current_order)
                ->where('display_order', '<=', $desired_order)
                ->update(['display_order' => \DB::raw('display_order - 1')]);
        } else {
            ManualCategory::withTrashed()
                ->where('display_order', '>=', $desired_order)
                ->where('display_order', '<', $current_order)
                ->update(['display_order' => \DB::raw('display_order + 1')]);
        }
        ManualCategory::withTrashed()
            ->where('display_order', 0)
            ->update(['display_order' => $desired_order]);

        return [];
    }

    public function destroy($id)
    {
        $category = ManualCategory::withTrashed()->findOrFail($id);
        $deleted = $category->forceDelete();
        $subcategories = ManualSubcategory::withTrashed()->where('category_id', $id)->get();
        foreach ($subcategories as $subcategory) {
            $subcategory->forceDelete();
        }

        return $category;
    }
}
