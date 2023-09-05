<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\ManualCandidate;

class ManualCandidateController extends AdminController
{
    public function toggleActive($id)
    {
        $candidate = ManualCandidate::withTrashed()->findOrFail($id);
        if ($candidate->trashed()) {
            $candidate->restore();
        } else {
            $candidate->delete();
        }

        return $candidate;
    }

    public function index(Request $request)
    {
        $category_id = $request->input('category_id', 0);
        $candidate_type_id = $request->input('candidate_type_id', 0);
        $name = $request->input('name', '');

        $inactive = filter_var($request->input('inactive', false), FILTER_VALIDATE_BOOLEAN);
        $candidates = ManualCandidate::with(['category', 'candidate_type'])
            ->where([
              'category_id' => $category_id,
              'candidate_type_id' => $candidate_type_id
            ]);
        if ($name) {
            $candidates = $candidates->where(function ($query) use ($name) {
                $query
                    ->orWhere('name', 'LIKE', "%$name%")
                    ->orWhere('slug', 'LIKE', "%$name%")
                    ->orWhere('name_es', 'LIKE', "%$name%")
                    ->orWhere('slug_es', 'LIKE', "%$name%");
            });
        }
        if ($inactive) {
            $candidates->onlyTrashed();
        }

        return $candidates->orderBy('name', 'ASC')->paginate(100);
    }

    public function all(Request $request)
    {
        $category_id = $request->input('category_id', 0);
        
        $inactive = filter_var($request->input('inactive', false), FILTER_VALIDATE_BOOLEAN);
        $candidates = ManualCandidate::with(['category', 'candidate_type'])
            ->where('category_id', $category_id);

        if ($inactive) {
            $candidates->onlyTrashed();
        }

        return $candidates->orderBy('name', 'ASC')->get();
    }

    public function search(Request $request)
    {
        $category_id = $request->input('category_id', 0);
        $name = $request->input('name', '');

        if (empty($name)) {
            return [];
        }

        $candidates = ManualCandidate::where('category_id', $category_id)
            ->where('name', 'LIKE', "{$name}%");

        return $candidates->orderBy('name', 'ASC')->get();
    }

    public function show($id)
    {
        $candidate = ManualCandidate::findOrFail($id);

        return $candidate;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'name' => 'required',
            'name_es' => 'required',
            'short_name' => 'required',
            'short_name_es' => 'required',
        ]);
        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = str_slug($data['name']);
        }
        if (empty($data['slug_es'])) {
          if (!empty($data['name_es'])) {
              $data['slug_es'] = str_slug($data['name_es']);
          }
          else {
              $data['slug_es'] = $data['slug'];
          }
        }
        $candidate = ManualCandidate::create($data);

        return $candidate;
    }

    public function update(Request $request, $id)
    {
        $candidate = ManualCandidate::withTrashed()->findOrFail($id);
        $this->validate($request, [
            'name' => 'required',
            'name_es' => 'required',
            'short_name' => 'required',
            'short_name_es' => 'required',
        ]);
        $data = $request->all();
        $candidate->fill($data);
        $candidate->save();

        return $candidate;
    }

    public function destroy($id)
    {
        $candidate = ManualCandidate::withTrashed()->findOrFail($id);
        $deleted = $candidate->forceDelete();

        return $candidate;
    }
}
