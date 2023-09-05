<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Facades\DataPull;
use App\ManualFuture;
use App\ManualFutureAnswer;

class ManualFutureController extends AdminController
{
    public function toggleActive($id)
    {
        $future = ManualFuture::withTrashed()->findOrFail($id);
        // $answers = ManualFutureAnswer::where('future_id', $id)->get();
        if ($future->trashed()) {
            $future->restore();
            // foreach ($answers as $answer) {
            //     $answer->restore();
            // }
        } else {
            $future->delete();
            // foreach ($answers as $answer) {
            //     $answer->delete();
            // }
        }

        return $future;
    }

    public function index(Request $request)
    {
        $page_id = $request->input('page_id', 0);
        $futures = ManualFuture::where('page_id', $page_id);

        return $futures->orderBy('display_order', 'ASC')->get();
    }

    public function show($id)
    {
        $future = ManualFuture::findOrFail($id);

        return $future;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'page_id' => 'required',
            'name' => 'required',
            'name_es' => 'required'
        ]);
        $data = $request->all();
        $data['display_order'] = ManualFuture::getNextDisplayOrder();
        $future = ManualFuture::create($data);

        return $future;
    }

    public function update(Request $request, $id)
    {
        $future = ManualFuture::withTrashed()->findOrFail($id);
        $this->validate($request, [
            'name' => 'required',
            'name_es' => 'required',
        ]);
        $data = $request->all();
        $future->fill($data);
        $future->save();

        return $future;
    }

    public function reorder(Request $request, $current_order, $desired_order)
    {
        $down = $desired_order > $current_order ? true : false;
        ManualFuture::withTrashed()
            ->where('display_order', $current_order)
            ->update(['display_order' => 0]);
        if ($down) {
            ManualFuture::withTrashed()
                ->where('display_order', '>', $current_order)
                ->where('display_order', '<=', $desired_order)
                ->update(['display_order' => \DB::raw('display_order - 1')]);
        } else {
            ManualFuture::withTrashed()
                ->where('display_order', '>=', $desired_order)
                ->where('display_order', '<', $current_order)
                ->update(['display_order' => \DB::raw('display_order + 1')]);
        }
        ManualFuture::withTrashed()
            ->where('display_order', 0)
            ->update(['display_order' => $desired_order]);
        
        $page_id = $request->input('page_id', 0);
        $futures = ManualFuture::where('page_id', $page_id)->orderBy('display_order', 'ASC')->get();
        $future_list = [];
        foreach ($futures as $future) {
            $answers = ManualFutureAnswer::with(['candidate'])
                ->where('future_id', $future->id)
                ->orderBy('display_order', 'ASC')->get();
            $future_list[] = [
                'poll' => $future,
                'answers' => $answers
            ];
        }
        return $future_list;
    }

    public function destroy($id)
    {
        $future = ManualFuture::withTrashed()->findOrFail($id);
        $deleted = $future->forceDelete();
        $answers = ManualFutureAnswer::where('future_id', $id)->get();
        foreach ($answers as $answer) {
            $answer->forceDelete();
        }

        return $future;
    }
}
