<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Facades\DataPull;
use App\ManualEvent;
use App\ManualEventBetType;
use App\ManualCandidate;
use App\ManualPollPage;
use App\ManualEventVote;
use App\Facades\Calculation;
use App\Facades\Evaluation;

class ManualEventController extends AdminController
{
    public function toggleActive($id)
    {
        $event = ManualEvent::withTrashed()->findOrFail($id);
        // $answers = ManualEventBetType::where('event_id', $id)->get();
        if ($event->trashed()) {
            $event->restore();
            // foreach ($answers as $answer) {
            //     $answer->restore();
            // }
        } else {
            $event->delete();
            // foreach ($answers as $answer) {
            //     $answer->delete();
            // }
        }

        return $event;
    }

    public function index(Request $request)
    {
        $page_id = $request->input('page_id', 0);
        $events = ManualEvent::with(['page', 'candidate1', 'candidate2'])->where('page_id', $page_id);

        return $events->orderBy('display_order', 'ASC')->get();
    }

    public function show($id)
    {
        $event = ManualEvent::with(['page', 'candidate1', 'candidate2'])->findOrFail($id);

        return $event;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'page_id' => 'required',
            'name' => 'required',
            'name_es' => 'required',
            'candidate1_name' => 'required',
            'candidate2_name' => 'required',
            'category_id' => 'required'
        ]);
        $data = $request->except([
          'candidate1_name', 'candidate2_name', 'category_id', 'candidate1_spread', 'candidate2_spread'
        ]);
        $data['spread'] = $request->input('candidate1_spread');
        $candidate1_name = $request->input('candidate1_name');
        $candidate1 = ManualCandidate::where('name', $candidate1_name)->first();
        if (!$candidate1) {
          $candidate1 = ManualCandidate::create([
            'name' => $candidate1_name,
            'name_es' => $candidate1_name,
            'category_id' => $request->input('category_id')
          ]);
        }
        $candidate2_name = $request->input('candidate2_name');
        $candidate2 = ManualCandidate::where('name', $candidate2_name)->first();
        if (!$candidate2) {
          $candidate2 = ManualCandidate::create([
            'name' => $candidate2_name,
            'name_es' => $candidate2_name,
            'category_id' => $request->input('category_id')
          ]);
        }
        $data['candidate1_id'] = $candidate1->id;
        $data['candidate2_id'] = $candidate2->id;
        $data['display_order'] = ManualEvent::getNextDisplayOrder();
        $event = ManualEvent::create($data);

        if ($event->candidate1_odds != 0 || $event->candidate2_odds != 0) {
          ManualEventBetType::create([
            'page_id' => $event->page_id,
            'event_id' => $event->id,
            'bet_type_id' => 2,
          ]);
        }
        if ($event->spread != 0) {
          ManualEventBetType::create([
            'page_id' => $event->page_id,
            'event_id' => $event->id,
            'bet_type_id' => 1,
          ]);
        }
        if ($event->over_under != 0) {
          ManualEventBetType::create([
            'page_id' => $event->page_id,
            'event_id' => $event->id,
            'bet_type_id' => 3,
          ]);
        }

        return $event->load(['candidate1', 'candidate2']);
    }

    public function update(Request $request, $id)
    {
        $event = ManualEvent::withTrashed()->findOrFail($id);
        $this->validate($request, [
          'name' => 'required',
          'name_es' => 'required',
          'candidate1_name' => 'required',
          'candidate2_name' => 'required',
        ]);

        $original_calculated_at = null;
        if ($event->calculated) {
            $original_calculated_at = $event->calculated_at;
            Calculation::cancelEventCalculation($event);
        }

        $data = $request->except([
          'candidate1_name', 'candidate2_name', 'category_id', 'candidate1_spread', 'candidate2_spread'
        ]);
        $data['spread'] = $request->input('candidate1_spread');
        $candidate1_name = $request->input('candidate1_name');
        $candidate1 = ManualCandidate::where('name', $candidate1_name)->first();
        if (!$candidate1) {
          $candidate1 = ManualCandidate::create([
            'name' => $candidate1_name,
            'name_es' => $candidate1_name,
            'category_id' => $request->input('category_id')
          ]);
        }
        $candidate2_name = $request->input('candidate2_name');
        $candidate2 = ManualCandidate::where('name', $candidate2_name)->first();
        if (!$candidate2) {
          $candidate2 = ManualCandidate::create([
            'name' => $candidate2_name,
            'name_es' => $candidate2_name,
            'category_id' => $request->input('category_id')
          ]);
        }
        $data['candidate1_id'] = $candidate1->id;
        $data['candidate2_id'] = $candidate2->id;
        
        $event->fill($data);
        $event->save();

        $moneyline = ManualEventBetType::where([
          'event_id' => $event->id,
          'bet_type_id' => 2
        ])->first();
        if (($event->candidate1_odds != 0 || $event->candidate2_odds != 0) && !$moneyline) {
          ManualEventBetType::create([
            'page_id' => $event->page_id,
            'event_id' => $event->id,
            'bet_type_id' => 2,
          ]);
        }
        else if ($event->candidate1_odds == 0 && $event->candidate2_odds == 0 && $moneyline) {
          $moneyline->forceDelete();
        }

        $spread = ManualEventBetType::where([
          'event_id' => $event->id,
          'bet_type_id' => 1
        ])->first();
        if ($event->spread != 0 && !$spread) {
          ManualEventBetType::create([
            'page_id' => $event->page_id,
            'event_id' => $event->id,
            'bet_type_id' => 1,
          ]);
        }
        else if ($event->spread == 0 && $spread) {
          $spread->forceDelete();
        }

        $over_under = ManualEventBetType::where([
          'event_id' => $event->id,
          'bet_type_id' => 3
        ])->first();
        if ($event->over_under != 0 && !$over_under) {
          ManualEventBetType::create([
            'page_id' => $event->page_id,
            'event_id' => $event->id,
            'bet_type_id' => 3,
          ]);
        }
        else if ($event->over_under == 0 && $over_under) {
          $over_under->forceDelete();
        }

        $modified_calculated_at = null;
        if ($event->page->status === ManualPollPage::$STATUS_ENDED) {
          $votes = ManualEventVote::where(['event_id' => $event->id, 'calculated' => false])->get();
          foreach ($votes as $vote) {
            Calculation::calculateEventVoteScore($vote);
          }
          $modified_calculated_at = $event->calculated_at;
        }

        Evaluation::reevaluate($original_calculated_at, $modified_calculated_at);

        return $event->load(['candidate1', 'candidate2']);
    }

    public function reorder(Request $request, $current_order, $desired_order)
    {
        $down = $desired_order > $current_order ? true : false;
        ManualEvent::withTrashed()
            ->where('display_order', $current_order)
            ->update(['display_order' => 0]);
        if ($down) {
            ManualEvent::withTrashed()
                ->where('display_order', '>', $current_order)
                ->where('display_order', '<=', $desired_order)
                ->update(['display_order' => \DB::raw('display_order - 1')]);
        } else {
            ManualEvent::withTrashed()
                ->where('display_order', '>=', $desired_order)
                ->where('display_order', '<', $current_order)
                ->update(['display_order' => \DB::raw('display_order + 1')]);
        }
        ManualEvent::withTrashed()
            ->where('display_order', 0)
            ->update(['display_order' => $desired_order]);
        
        $page_id = $request->input('page_id', 0);
        $events = ManualEvent::with(['candidate1', 'candidate2'])->where('page_id', $page_id)->orderBy('display_order', 'ASC')->get();
        
        return $events;
    }

    public function destroy($id)
    {
        $event = ManualEvent::withTrashed()->findOrFail($id);
        $deleted = $event->forceDelete();
        $answers = ManualEventBetType::where('event_id', $id)->get();
        foreach ($answers as $answer) {
            $answer->forceDelete();
        }

        return $event;
    }
}
