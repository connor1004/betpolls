<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Facades\Geoip;
use App\ManualCategory;
use App\ManualSubcategory;
use App\ManualPollPage;
use App\ManualFuture;
use App\ManualFutureAnswer;
use App\ManualFutureVote;
use App\ManualEvent;
use App\ManualEventBetType;
use App\ManualEventVote;
use App\Vote;
use App\Post;
use Carbon\Carbon;
use App\Facades\Data;


class FutureController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        // $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return $this->showManualPolls($request, 0, 0, 1);
    }

    public function showManualPolls(Request $request, $category_id, $subcategory_id, $is_future) {
        $geoip = Geoip::getGeoip();
        $timezone = $geoip ? $geoip->time_zone : 'UTC';
        
        $period = $request->input('period', 0);

        $page = Post::where('slug', $is_future ? 'futures' : 'sport')->where('post_type', Post::$POST_TYPE_PAGE)->first();

        $locale = app('translator')->getLocale();
        
        $all_categories = ManualCategory::orderBy('display_order', 'ASC')->get();
        $categories = [];
        foreach ($all_categories as $category) {
            if ($category->id == $category_id) {
                $categories[] = $category;
            }
            else {
                $count = ManualPollPage::where([
                    'category_id' => $category->id,
                    'is_future' => $is_future,
                    'published' => 1
                ])->count();
                if ($count > 0) {
                    $categories[] = $category;
                }
            }
        }

        $all_subcategories = ManualSubcategory::where('category_id', $category_id)
            ->orderBy('display_order', 'ASC')->get();
        $subcategories = [];
        foreach ($all_subcategories as $subcategory) {
            if ($subcategory->id == $subcategory_id) {
                $subcategories[] = $subcategory;
            }
            else {
                $count = ManualPollPage::where([
                    'subcategory_id' => $subcategory->id,
                    'is_future' => $is_future,
                    'published' => 1
                ])->count();
                if ($count > 0) {
                    $subcategories[] = $subcategory;
                }
            }
        }

        $currentDate = (new Carbon())->setTimezone('UTC')->format('Y-m-d H:i:s');
        $upcoming_polls = ManualPollPage::where('start_at', '>', $currentDate)
                ->where('is_future', $is_future)->where('published', 1);
        $past_polls = ManualPollPage::where('start_at', '<=', $currentDate)
                ->where('is_future', $is_future)->where('published', 1);
        $category = null;
        $subcategory = null;
        if ($category_id) {
            $upcoming_polls = $upcoming_polls->where('category_id', $category_id);
            $past_polls = $past_polls->where('category_id', $category_id);
            $category = ManualCategory::findOrFail($category_id);
            $page = $category;
        }
        if ($subcategory_id) {
            $upcoming_polls = $upcoming_polls->where('subcategory_id', $subcategory_id);
            $past_polls = $past_polls->where('subcategory_id', $subcategory_id);
            $subcategory = ManualSubcategory::findOrFail($subcategory_id);
            $page = $subcategory;
        }
        $upcoming_polls = $upcoming_polls->orderBy('start_at', 'ASC')->paginate(10, ['*'], 'page1');

        if ($period == 0) {
            $past_polls = $past_polls->orderBy('start_at', 'DESC')->limit(10);
        }
        else if ($period == 1) {
            $past_polls = $past_polls->orderBy('start_at', 'DESC');
        }
        else {
            $start_time = $period.'-01-01 00:00:00';
            // $start_time = Carbon::createFromFormat('Y-m-d H:i:s', $start_time, $timezone)
            //     ->setTimezone('UTC')->format('Y-m-d H:i:s');
            $start_time = Carbon::createFromFormat('Y-m-d H:i:s', $start_time, 'America/New_York')
                ->setTimezone('UTC')->format('Y-m-d H:i:s');
            $end_time = ($period + 1).'-01-01 00:00:00';
            // $end_time = Carbon::createFromFormat('Y-m-d H:i:s', $end_time, $timezone)
            //     ->setTimezone('UTC')->format('Y-m-d H:i:s');
            $end_time = Carbon::createFromFormat('Y-m-d H:i:s', $end_time, 'America/New_York')
                ->setTimezone('UTC')->format('Y-m-d H:i:s');
            $past_polls = $past_polls->where('start_at', '>=', $start_time)
                ->where('start_at', '<', $end_time)->orderBy('start_at', 'ASC');
        }

        $past_polls = $past_polls->paginate(10, ['*'], 'page2');

        $min_start_time = ManualPollPage::min('start_at');
        if ($min_start_time) {
            // $min_year = Carbon::createFromFormat('Y-m-d H:i:s', $min_start_time, $timezone)->year;
            $min_year = Carbon::createFromFormat('Y-m-d H:i:s', $min_start_time)->setTimezone('America/New_York')->year;
        }
        else {
            // $min_year = (new Carbon(null, $timezone))->year;
            $min_year = (new Carbon())->setTimezone('America/New_York')->year;
        }
        // $max_year = (new Carbon(null, $timezone))->year;
        $max_year = (new Carbon())->setTimezone('America/New_York')->year;

        return view('front.future', [
            'page' => $page,
            'categories' => $categories,
            'cur_category' => $category,
            'subcategories' => $subcategories,
            'cur_subcategory' => $subcategory,
            'min_year' => $min_year,
            'max_year' => $max_year,
            'category_id' => $category_id,
            'subcategory_id' => $subcategory_id,
            'period' => $period,
            'upcoming_polls' => $upcoming_polls,
            'past_polls' => $past_polls,
            'is_future' => $is_future
        ]);
    }

    public function showPollPage(Request $request, $poll_page) {
        $polls = [];
        if ($poll_page->is_future == 1) {
            $futures = ManualFuture::where([
                'page_id' => $poll_page->id,
                'published' => 1
            ])->orderBy('display_order', 'ASC')->get();
            foreach ($futures as $future) {
                $answers = ManualFutureAnswer::where('future_id', $future->id)
                    ->orderBy('display_order', 'ASC')->get();
                $polls[] = [
                    'poll' => $future,
                    'answers' => $answers
                ];
            }
        } else {
            $events = ManualEvent::where([
                'page_id' => $poll_page->id,
                'published' => 1
            ])->orderBy('display_order', 'ASC')->get();
            foreach ($events as $event) {
                $bet_types = ManualEventBetType::where('event_id', $event->id)
                    ->orderBy('bet_type_id', 'ASC')->get();
                $polls[] = [
                    'poll' => $event,
                    'bet_types' => $bet_types
                ];
            }
        }

        return view('front.future-poll', [
            'page' => $poll_page,
            'poll_page' => $poll_page,
            'polls' => $polls
        ]);
    }

    public function showFuturePollPage(Request $request, $mainSlug, $subSlug, $pageSlug) {
        $locale = app('translator')->getLocale();
        $category = null;
        if ($locale === 'es') {
            $category = ManualCategory::where('slug_es', $mainSlug)->first();
        }
        if (!$category) {
            $category = ManualCategory::where('slug', $mainSlug)->first();
        }
        if (!$category) {
            return view('front.404');
        }
        $subcategory = null;
        if ($locale === 'es') {
            $subcategory = ManualSubcategory::where([
                'slug_es' => $subSlug,
                'category_id' => $category->id    
            ])->first();
        }
        if (!$subcategory) {
            $subcategory = ManualSubcategory::where([
                'slug' => $subSlug,
                'category_id' => $category->id    
            ])->first();
        }
        if (!$subcategory) {
            return view('front.404');
        }
        $poll_page = null;
        if ($locale === 'es') {
            $poll_page = ManualPollPage::where([
                'slug_es' => $pageSlug,
                'category_id' => $category->id,
                'subcategory_id' => $subcategory->id,
                'is_future' => 1
            ])->first();
        }
        if (!$poll_page) {
            $poll_page = ManualPollPage::where([
                'slug' => $pageSlug,
                'category_id' => $category->id,
                'subcategory_id' => $subcategory->id,
                'is_future' => 1
            ])->first();
        }
        if (!$poll_page) {
            return view('front.404');
        }

        return $this->showPollPage($request, $poll_page);
    }

    public function showFutureSubOrPage(Request $request, $mainSlug, $subOrPageSlug) {
        $locale = app('translator')->getLocale();
        $category = null;
        if ($locale === 'es') {
            $category = ManualCategory::where('slug_es', $mainSlug)->first();
        }
        if (!$category) {
            $category = ManualCategory::where('slug', $mainSlug)->first();
        }
        if (!$category) {
            return view('front.404');
        }
        $subcategory = null;
        if ($locale === 'es') {
            $subcategory = ManualSubcategory::where([
                'slug_es' => $subOrPageSlug,
                'category_id' => $category->id    
            ])->first();
        }
        if (!$subcategory) {
            $subcategory = ManualSubcategory::where([
                'slug' => $subOrPageSlug,
                'category_id' => $category->id    
            ])->first();
        }
        if ($subcategory) {
            return $this->showManualPolls($request, $category->id, $subcategory->id, 1);
        }
        $poll_page = null;
        if ($locale === 'es') {
            $poll_page = ManualPollPage::where([
                'slug_es' => $subOrPageSlug,
                'category_id' => $category->id,
                'subcategory_id' => 0,
                'is_future' => 1
            ])->first();
        }
        if (!$poll_page) {
            $poll_page = ManualPollPage::where([
                'slug' => $subOrPageSlug,
                'category_id' => $category->id,
                'subcategory_id' => 0,
                'is_future' => 1
            ])->first();
        }
        if (!$poll_page) {
            return view('front.404');
        }

        return $this->showPollPage($request, $poll_page);
    }

    public function showFutureMain(Request $request, $mainSlug) {
        $locale = app('translator')->getLocale();
        $category = null;
        if ($locale === 'es') {
            $category = ManualCategory::where('slug_es', $mainSlug)->first();
        }
        if (!$category) {
            $category = ManualCategory::where('slug', $mainSlug)->first();
        }
        if (!$category) {
            return view('front.404');
        }
        return $this->showManualPolls($request, $category->id, 0, 1);
    }

    public function showSportPollPage(Request $request, $mainSlug, $subSlug, $pageSlug) {
        $locale = app('translator')->getLocale();
        $category = null;
        if ($locale === 'es') {
            $category = ManualCategory::where('slug_es', $mainSlug)->first();
        }
        if (!$category) {
            $category = ManualCategory::where('slug', $mainSlug)->first();
        }
        if (!$category) {
            return view('front.404');
        }
        $subcategory = null;
        if ($locale === 'es') {
            $subcategory = ManualSubcategory::where([
                'slug_es' => $subSlug,
                'category_id' => $category->id    
            ])->first();
        }
        if (!$subcategory) {
            $subcategory = ManualSubcategory::where([
                'slug' => $subSlug,
                'category_id' => $category->id    
            ])->first();
        }
        if (!$subcategory) {
            return view('front.404');
        }
        $poll_page = null;
        if ($locale === 'es') {
            $poll_page = ManualPollPage::where([
                'slug_es' => $pageSlug,
                'category_id' => $category->id,
                'subcategory_id' => $subcategory->id,
                'is_future' => 0
            ])->first();
        }
        if (!$poll_page) {
            $poll_page = ManualPollPage::where([
                'slug' => $pageSlug,
                'category_id' => $category->id,
                'subcategory_id' => $subcategory->id,
                'is_future' => 0
            ])->first();
        }
        if (!$poll_page) {
            return view('front.404');
        }

        return $this->showPollPage($request, $poll_page);
    }

    public function showSportSubOrPage(Request $request, $mainSlug, $subOrPageSlug) {
        $locale = app('translator')->getLocale();
        $category = null;
        if ($locale === 'es') {
            $category = ManualCategory::where('slug_es', $mainSlug)->first();
        }
        if (!$category) {
            $category = ManualCategory::where('slug', $mainSlug)->first();
        }
        if (!$category) {
            return view('front.404');
        }
        $subcategory = null;
        if ($locale === 'es') {
            $subcategory = ManualSubcategory::where([
                'slug_es' => $subOrPageSlug,
                'category_id' => $category->id    
            ])->first();
        }
        if (!$subcategory) {
            $subcategory = ManualSubcategory::where([
                'slug' => $subOrPageSlug,
                'category_id' => $category->id    
            ])->first();
        }
        if ($subcategory) {
            return $this->showManualPolls($request, $category->id, $subcategory->id, 0);
        }
        $poll_page = null;
        if ($locale === 'es') {
            $poll_page = ManualPollPage::where([
                'slug_es' => $subOrPageSlug,
                'category_id' => $category->id,
                'subcategory_id' => 0,
                'is_future' => 0
            ])->first();
        }
        if (!$poll_page) {
            $poll_page = ManualPollPage::where([
                'slug' => $subOrPageSlug,
                'category_id' => $category->id,
                'subcategory_id' => 0,
                'is_future' => 0
            ])->first();
        }
        if (!$poll_page) {
            return view('front.404');
        }

        return $this->showPollPage($request, $poll_page);
    }

    public function showSportMain(Request $request, $mainSlug) {
        $locale = app('translator')->getLocale();
        $category = null;
        if ($locale === 'es') {
            $category = ManualCategory::where('slug_es', $mainSlug)->first();
        }
        if (!$category) {
            $category = ManualCategory::where('slug', $mainSlug)->first();
        }
        if (!$category) {
            return view('front.404');
        }
        return $this->showManualPolls($request, $category->id, 0, 0);
    }

    public function show($id) {
        $poll_page = ManualPollPage::findOrFail($id);
        $polls = [];
        if ($poll_page->is_future == 1) {
            $futures = ManualFuture::where('page_id', $id)
                ->orderBy('display_order', 'ASC')->get();
            foreach ($futures as $future) {
                $answers = ManualFutureAnswer::where('future_id', $future->id)
                    ->orderBy('display_order', 'ASC')->get();
                $polls[] = [
                    'poll' => $future,
                    'answers' => $answers
                ];
            }
        } else {
            $events = ManualEvent::where('page_id', $id)
                ->orderBy('display_order', 'ASC')->get();
            foreach ($events as $event) {
                $bet_types = ManualEventBetType::where('event_id', $event->id)
                    ->orderBy('bet_type_id', 'ASC')->get();
                $polls[] = [
                    'poll' => $event,
                    'bet_types' => $bet_types
                ];
            }
        }

        return view('front.future-poll', [
            'page' => $poll_page,
            'poll_page' => $poll_page,
            'polls' => $polls
        ]);
    }

    public function event_vote(Request $request, $event_id) {
        $user = Data::getUser();
        $event = ManualEvent::findOrFail($event_id);
        if ($event->page->status !== ManualPollPage::$STATUS_NOT_STARTED 
        || $event->page->start_at < (new Carbon())->format('Y-m-d H:i:s')) {
            return view('front.snippets.event-game', [
                'poll_page' => $event->page,
                'poll_content' => [
                    'poll' => $event,
                    'bet_types' => ManualEventBetType::where('event_id', $event->id)
                        ->orderBy('bet_type_id', 'ASC')->get()
                ],
                'user' => $user,
                'active' => true,
                'vote_error' => 'Cannot vote! Please refresh the page'
            ]);
        }
        $votesParams = $request->input('votes', []);

        $voterCount = 0;
        $voteCount = 0;

        foreach ($votesParams as $voteParams) {
            $eventBetType = ManualEventBetType::findOrFail($voteParams['event_bet_type_id']);
            $vote = ManualEventVote::where(['event_bet_type_id' => $eventBetType->id, 'user_id' => $user->id])->first();

            if (!$vote) {
                if (!isset($voteParams['vote_case'])) {
                    continue;
                }
                $vote_case = $voteParams['vote_case'];
                $vote = ManualEventVote::create([
                    'event_bet_type_id' => $eventBetType->id,
                    'event_id' => $eventBetType->event_id,
                    'page_id' => $eventBetType->page_id,
                    'bet_type_id' => $eventBetType->bet_type_id,
                    'user_id' => $user->id,
                    'vote_case' => $vote_case
                ]);
                
                switch ($vote_case) {
                    case Vote::$VOTE_CASE_WIN:
                        $eventBetType->win_vote_count++;
                        break;
                    case Vote::$VOTE_CASE_LOSS:
                        $eventBetType->loss_vote_count++;
                        break;
                    case Vote::$VOTE_CASE_TIE:
                        $eventBetType->tie_vote_count++;
                        break;
                    default:
                        break;
                }
                $eventBetType->save();
            }
            // if ($vote) {
            //     if ($user->role !== User::$ROLE_UNKNOWN) {
            //         Leaderboard::addVoteCount($game, $vote);
            //     }
            // }
            $voteCount ++;
            $voterCount = 1;
        }

        $event->voter_count += $voterCount;
        $event->vote_count += $voteCount;
        $event->save();

        return view('front.snippets.event-game', [
            'poll_page' => $event->page,
            'poll_content' => [
                'poll' => $event,
                'bet_types' => ManualEventBetType::where('event_id', $event->id)
                    ->orderBy('bet_type_id', 'ASC')->get()
            ],
            'user' => $user,
            'active' => true,
        ]);
    }

    public function future_vote(Request $request, $future_id) {
        $user = Data::getUser();
        $future = ManualFuture::findOrFail($future_id);
        if ($future->page->status !== ManualPollPage::$STATUS_NOT_STARTED 
        || $future->page->start_at < (new Carbon())->format('Y-m-d H:i:s')) {
            return view('front.snippets.future-game', [
                'poll_page' => $future->page,
                'poll_content' => [
                    'poll' => $future,
                    'answers' => ManualFutureAnswer::where('future_id', $future->id)
                        ->orderBy('display_order', 'ASC')->get()
                ],
                'user' => $user,
                'active' => true,
                'vote_error' => 'Cannot vote! Please refresh the page'
            ]);
        }
        $voted_answer_id = $request->input('voted_answer', 0);

        if ($voted_answer_id) {
            $voted_answer = ManualFutureAnswer::findOrFail($voted_answer_id);
            $vote = ManualFutureVote::where([
                'answer_id' => $voted_answer->id,
                'user_id' => $user->id
            ])->first();

            if (!$vote) {
                $vote = ManualFutureVote::create([
                    'page_id' => $voted_answer->page_id,
                    'future_id' => $voted_answer->future_id,
                    'user_id' => $user->id,
                    'answer_id' => $voted_answer_id,
                ]);
                $future->voter_count ++;
                $future->save();
                $voted_answer->vote_count ++;
                $voted_answer->save();
            }
        }

        return view('front.snippets.future-game', [
            'poll_page' => $future->page,
            'poll_content' => [
                'poll' => $future,
                'answers' => ManualFutureAnswer::where('future_id', $future->id)
                    ->orderBy('display_order', 'ASC')->get()
            ],
            'user' => $user,
            'active' => true,
        ]);
    }
}
