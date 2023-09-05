<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\ManualSubcategory;

class ManualSubcategoryController extends AdminController
{
    public function toggleActive($id)
    {
        $subcategory = ManualSubcategory::withTrashed()->findOrFail($id);
        if ($subcategory->trashed()) {
            $subcategory->restore();
        } else {
            $subcategory->delete();
        }

        return $subcategory;
    }

    public function index(Request $request)
    {
        $category_id = $request->input('category_id', 0);
        $inactive = filter_var($request->input('inactive', false), FILTER_VALIDATE_BOOLEAN);
        $subcategories = ManualSubcategory::with(['category'])->where('category_id', $category_id);
        $name = $request->input('name');
        if ($name) {
          $subcategories = $subcategories->where(function ($query) use ($name) {
              $query
                  ->orWhere('name', 'LIKE', "%$name%")
                  ->orWhere('slug', 'LIKE', "%$name%")
                  ->orWhere('name_es', 'LIKE', "%$name%")
                  ->orWhere('slug_es', 'LIKE', "%$name%");
          });
        }
        if ($inactive) {
            $subcategories->onlyTrashed();
        }

        return $subcategories->orderBy('name', 'ASC')->get();
    }

    public function all(Request $request)
    {
        $subcategories = ManualSubcategory::with(['category'])->get()
            ->sortBy(function ($item1) {
                return (empty($item1->category) ? 100 : $item1->category->display_order) * 100000 + $item1->display_order;
            })->values();

        return $subcategories;
    }

    public function show($id)
    {
        $subcategory = ManualSubcategory::findOrFail($id);

        return $subcategory;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            // 'logo' => 'required',
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
        $data['display_order'] = ManualSubcategory::getNextDisplayOrder();
        $subcategory = ManualSubcategory::create($data);

        return $subcategory;
    }

    public function update(Request $request, $id)
    {
        $subcategory = ManualSubcategory::withTrashed()->findOrFail($id);
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
        $subcategory->fill($data);
        $subcategory->save();

        return $subcategory;
    }

    public function reorder(Request $request, $category_id, $current_order, $desired_order)
    {
        $down = $desired_order > $current_order ? true : false;
        ManualSubcategory::where('display_order', $current_order)
            ->where('category_id', $category_id)
            ->update(['display_order' => 0]);
        if ($down) {
            ManualSubcategory::where('display_order', '>', $current_order)
                ->where('display_order', '<=', $desired_order)
                ->where('category_id', $category_id)
                ->update(['display_order' => \DB::raw('display_order - 1')]);
        } else {
            ManualSubcategory::where('display_order', '>=', $desired_order)
                ->where('display_order', '<', $current_order)
                ->where('category_id', $category_id)
                ->update(['display_order' => \DB::raw('display_order + 1')]);
        }
        ManualSubcategory::where('display_order', 0)
            ->where('category_id', $category_id)
            ->update(['display_order' => $desired_order]);

        return [];
    }

    public function destroy($id)
    {
        $subcategory = ManualSubcategory::withTrashed()->findOrFail($id);
        $deleted = $subcategory->forceDelete();

        return $subcategory;
    }
}
