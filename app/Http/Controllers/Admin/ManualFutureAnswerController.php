<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\ManualPollPage;
use App\ManualFuture;
use App\ManualFutureAnswer;
use App\ManualFutureVote;
use App\ManualCandidate;
use App\Facades\Calculation;
use App\Facades\Evaluation;

class ManualFutureAnswerController extends AdminController
{
    public function index(Request $request)
    {
        $future_id = $request->input('future_id', 0);
        $answers = ManualFutureAnswer::where('future_id', $future_id);

        return $answers->orderBy('display_order', 'ASC')->get();
    }

    public function show($id)
    {
        $future = ManualFutureAnswer::findOrFail($id);

        return $future;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'page_id' => 'required',
            'future_id' => 'required',
            'name' => 'required',
            'category_id' => 'required'
        ]);
        $data = $request->except(['name', 'category_id']);
        $name = $request->input('name');
        $candidate = ManualCandidate::where('name', $name)->first();
        if (!$candidate) {
          $candidate = ManualCandidate::create([
            'name' => $name,
            'name_es' => $name,
            'category_id' => $request->input('category_id')
          ]);
        }
        $data['candidate_id'] = $candidate->id;
        $data['display_order'] = ManualFutureAnswer::getNextDisplayOrder();
        $answer = ManualFutureAnswer::create($data);

        return $answer->load('candidate');
    }

    public function update(Request $request, $id)
    {
        $answer = ManualFutureAnswer::findOrFail($id);
        $this->validate($request, [
            'name' => 'required',
        ]);
        
        $future = $answer->future;
        $original_calculated_at = null;
        if ($future->calculated) {
            $original_calculated_at = $future->calculated_at;
            Calculation::cancelFutureCalculation($future);
        }

        $data = $request->except('name');
        $name = $request->input('name');
        if ($name != $answer->candidate->name) {
          $candidate = ManualCandidate::where('name', $name)->first();
          if (!$candidate) {
            $candidate = ManualCandidate::create([
              'name' => $name,
              'name_es' => $name,
              'category_id' => $answer->page->category_id
            ]);
          }
          $data['candidate_id'] = $candidate->id;
        }
        $answer->fill($data);
        $answer->save();

        $modified_calculated_at = null;
        if ($future->page->status === ManualPollPage::$STATUS_ENDED) {
          $votes = ManualFutureVote::where(['future_id' => $future->id, 'calculated' => false])->get();
          foreach ($votes as $vote) {
            Calculation::calculateFutureVoteScore($vote);
          }
          $modified_calculated_at = $future->calculated_at;
        }

        Evaluation::reevaluate($original_calculated_at, $modified_calculated_at);

        return $answer->load('candidate');
    }

    public function reorder(Request $request, $current_order, $desired_order)
    {
        $down = $desired_order > $current_order ? true : false;
        ManualFutureAnswer::where('display_order', $current_order)
            ->update(['display_order' => 0]);
        if ($down) {
            ManualFutureAnswer::where('display_order', '>', $current_order)
                ->where('display_order', '<=', $desired_order)
                ->update(['display_order' => \DB::raw('display_order - 1')]);
        } else {
            ManualFutureAnswer::where('display_order', '>=', $desired_order)
                ->where('display_order', '<', $current_order)
                ->update(['display_order' => \DB::raw('display_order + 1')]);
        }
        ManualFutureAnswer::where('display_order', 0)
            ->update(['display_order' => $desired_order]);
        
        $future_id = $request->input('future_id', 0);
        $answers = ManualFutureAnswer::with(['candidate'])
                    ->where('future_id', $future_id)
                    ->orderBy('display_order', 'ASC')->get();
        
        return $answers;
    }

    public function destroy($id)
    {
        $future = ManualFutureAnswer::findOrFail($id);
        $deleted = $future->forceDelete();

        return $future;
    }
}
