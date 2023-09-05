<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

use App\Facades\Geoip;
use App\ManualPollPage;
use App\ManualFuture;
use App\ManualFutureAnswer;
use App\ManualEvent;
use App\ManualEventBetType;
use App\Facades\Calculation;
use App\Facades\Evaluation;
use DB;
use Carbon\Carbon;

class ManualPollPageController extends AdminController
{
    public function index(Request $request)
    {
        $geoip = Geoip::getGeoip();
        $timezone = $geoip ? $geoip->time_zone : 'UTC';

        $timezone = Geoip::getGeoip() ? Geoip::getGeoip()->time_zone : 'UTC';
        $date = $request->get('start_at', (new Carbon)->setTimezone($timezone)->format('Y-m-d'));
        $start_at = Carbon::createFromFormat('Y-m-d', $date, $timezone)->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');

        $date = $request->get('end_at', (new Carbon)->setTimezone($timezone)->format('Y-m-d'));
        $end_at = Carbon::createFromFormat('Y-m-d', $date, $timezone)->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');

        $category_id = $request->input('category_id');
        $inactive = filter_var($request->input('inactive', false), FILTER_VALIDATE_BOOLEAN);
        $poll_pages = ManualPollPage::with(['category', 'subcategory'])
            ->where('category_id', $category_id)
            ->whereBetween('start_at', [$start_at, $end_at]);

        $subcategory_id = $request->input('subcategory_id', 0);
        if ($subcategory_id) {
            $poll_pages = $poll_pages->where('subcategory_id', $subcategory_id);
        }
        if ($inactive) {
            $poll_pages->onlyTrashed();
        }

        return $poll_pages->orderBy('start_at')->get();
    }

    public function replicates(Request $request) {
        $category_id = $request->input('category_id');
        $is_future = $request->input('is_future', 1);
        $id = $request->input('id', 0);

        $poll_pages = ManualPollPage::where([
            'category_id' => $category_id,
            'is_future' => $is_future
        ])->where('id', '!=', $id)->orderBy('created_at', 'DESC')->get();

        return $poll_pages;
    }

    public function replicate(Request $request, $id, $replicate_id) {
        $poll_page = ManualPollPage::findOrFail($id);
        if ($poll_page->is_future == 1) {
            $futures = ManualFuture::where('page_id', $id)->get();
            foreach ($futures as $future) {
                $future->forceDelete();
            }
            $futures = ManualFuture::where('page_id', $replicate_id)->orderBy('display_order', 'ASC')->get();
            foreach ($futures as $future) {
                $future1 = ManualFuture::create([
                    'page_id' => $poll_page->id,
                    'name' => $future->name,
                    'name_es' => $future->name_es,
                    'published' => 0,
                    'display_order' => ManualFuture::getNextDisplayOrder(),
                ]);
                $answers = ManualFutureAnswer::where('future_id', $future->id)
                    ->orderBy('display_order', 'ASC')->get();
                foreach($answers as $answer) {
                    $answer1 = ManualFutureAnswer::create([
                        'page_id' => $poll_page->id,
                        'future_id' => $future1->id,
                        'candidate_id' => $answer->candidate_id,
                        'display_order' => ManualFutureAnswer::getNextDisplayOrder(),
                        'standing' => $answer->standing,
                        'odds' => $answer->odds,
                        'winning_points' => $answer->winning_points,
                        'losing_points' => $answer->losing_points
                    ]);
                }
            }
            return $this->futures($id);
        } else {
            $events = ManualEvent::where('page_id', $id)->get();
            foreach ($events as $event) {
                $event->forceDelete();
            }
            $events = ManualEvent::where('page_id', $replicate_id)->orderBy('display_order', 'ASC')->get();
            foreach ($events as $event) {
                $event1 = ManualEvent::create([
                    'page_id' => $poll_page->id,
                    'name' => $event->name,
                    'name_es' => $event->name_es,
                    'candidate1_id' => $event->candidate1_id,
                    'candidate1_standing' => $event->candidate1_standing,
                    'candidate1_odds' => $event->candidate1_odds,
                    'candidate2_id' => $event->candidate2_id,
                    'candidate2_standing' => $event->candidate2_standing,
                    'candidate2_odds' => $event->candidate2_odds,
                    'tie_odds' => $event->tie_odds,
                    'spread' => $event->spread,
                    'over_under' => $event->over_under,
                    'meta' => $event->meta,
                    'meta_es' => $event->meta_es,
                    'spread_win_points' => $event->spread_win_points,
                    'spread_loss_points' => $event->spread_loss_points,
                    'moneyline1_win_points' => $event->moneyline1_win_points,
                    'moneyline1_loss_points' => $event->moneyline1_loss_points,
                    'moneyline2_win_points' => $event->moneyline2_win_points,
                    'moneyline2_loss_points' => $event->moneyline2_loss_points,
                    'moneyline_tie_win_points' => $event->moneyline_tie_win_points,
                    'moneyline_tie_loss_points' => $event->moneyline_tie_loss_points,
                    'over_under_win_points' => $event->over_under_win_points,
                    'over_under_loss_points' => $event->over_under_loss_points,
                    'display_order' => ManualEvent::getNextDisplayOrder(),
                    'published' => 0
                ]);
                $bet_types = ManualEventBetType::where('event_id', $event->id)
                    ->orderBy('display_order', 'ASC')->get();
                foreach($bet_types as $bet_type) {
                    $bet_type1 = ManualEventBetType::create([
                        'page_id' => $poll_page->id,
                        'event_id' => $event1->id,
                        'bet_type_id' => $bet_type->bet_type_id
                    ]);
                }
            }
            return ManualEvent::with(['candidate1', 'candidate2'])
                ->where('page_id', $id)
                ->orderBy('display_order', 'ASC')->get();
        }
    }

    public function toggleActive($id)
    {
        $poll_page = ManualPollPage::withTrashed()->findOrFail($id);
        if ($poll_page->trashed()) {
            $poll_page->restore();
        } else {
            $poll_page->delete();
        }

        return $poll_page;
    }

    public function toggleActivePollPages(Request $request)
    {
        $poll_pages = ManualPollPage::withTrashed()->whereIn('id', $request->input('poll_pages'))->get();
        foreach ($poll_pages as $poll_page) {
            if ($poll_page->trashed()) {
                $poll_page->restore();
            } else {
                $poll_page->delete();
            }
        }
        return $poll_pages;
    }

    public function show($id)
    {
        $poll_page = ManualPollPage::withTrashed()->findOrFail($id);
        $poll_page->load(['subcategory']);
        return $poll_page;
    }

    public function showWhole($id)
    {
        $poll_page = ManualPollPage::with(['category', 'subcategory'])->findOrFail($id);
        $polls = [];
        if ($poll_page->is_future) {
            $polls = $this->futures($id);
        }
        else {
            $polls = ManualEvent::with(['candidate1', 'candidate2'])
                ->where('page_id', $id)
                ->orderBy('display_order', 'ASC')->get();
        }
        return [
            'page' => $poll_page,
            'polls' => $polls
        ];
    }

    public function futures($page_id) {
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

    public function store(Request $request)
    {
        $this->validate($request, [
            'start_at' => 'required|date_format:Y-m-d H:i:s',
            'category_id' => 'required|numeric',
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
        $poll_page = ManualPollPage::create($data);
        $poll_page = ManualPollPage::with(['subcategory'])->find($poll_page->id);

        return $poll_page;
    }

    public function update(Request $request, $id)
    {
        $poll_page = ManualPollPage::withTrashed()->findOrFail($id);
        $original_calculated_at = null;
        if ($poll_page->calculated) {
            $original_calculated_at = $poll_page->calculated_at;
            Calculation::cancelPollPageCalculation($poll_page);
        }
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
        if ($poll_page->is_future != $data['is_future']) {
            if ($poll_page->is_future == 1) {
                $futures = ManualFuture::where('page_id', $poll_page->id)->get();
                foreach ($futures as $future) {
                    $future->forceDelete();
                }
            } else {
                $events = ManualEvent::where('page_id', $poll_page->id)->get();
                foreach ($events as $event) {
                    $event->forceDelete();
                }
            }
        }
        $poll_page->fill($data);
        $poll_page->save();
        $modified_calculated_at = null;
        if ($poll_page->status === ManualPollPage::$STATUS_ENDED) {
            Calculation::calculatePollPageScore($poll_page);
            $modified_calculated_at = $poll_page->calculated_at;
        }

        Evaluation::reevaluate($original_calculated_at, $modified_calculated_at);

        $poll_page->load(['category', 'subcategory']);

        return $poll_page;
    }

    public function destroy($id)
    {
        $poll_page = ManualPollPage::withTrashed()->findOrFail($id);
        $poll_page->forceDelete();

        return $poll_page;
    }

    public function destroyManualPollPages(Request $request)
    {
        $poll_pages = ManualPollPage::withTrashed()->whereIn('id', $request->input('poll_pages'))->get();
        foreach ($poll_pages as $poll_page) {
            $poll_page->forceDelete();
        }
        return $poll_pages;
    }
}
