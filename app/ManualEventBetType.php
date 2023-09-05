<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Log;

class ManualEventBetType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'page_id', 'event_id', 'bet_type_id', 'win_vote_count',
        'loss_vote_count', 'tie_vote_count', 'matcned_vote_case',
        'calculated_at'
    ];

    public function page()
    {
        return $this->belongsTo('App\ManualPollPage');
    }

    public function event()
    {
        return $this->belongsTo('App\ManualEvent');
    }

    public function bet_type()
    {
        return $this->belongsTo('App\BetType');
    }

    public function getMatchCaseAttribute()
    {
        $event = $this->event;
        $page = $this->page;
        $bet_type = $this->bet_type;

        if ($page->status !== ManualPollPage::$STATUS_ENDED) {
            return false;
        }

        switch ($bet_type->value) {
            case BetType::$VALUE_SPREAD:
                $score_difference = $event->candidate1_score - $event->candidate2_score;
                if ($score_difference > -$event->spread) {
                    return Vote::$VOTE_CASE_WIN;
                } elseif ($score_difference < -$event->spread) {
                    return Vote::$VOTE_CASE_LOSS;
                } else {
                    return false;
                }
                break;
            case BetType::$VALUE_OVER_UNDER:
                $score_total = $event->over_under_score;
                if ($score_total > $event->over_under) {
                    return Vote::$VOTE_CASE_WIN;
                } elseif ($score_total < $event->over_under) {
                    return Vote::$VOTE_CASE_LOSS;
                } else {
                    return false;
                }
                break;
            case BetType::$VALUE_MONEYLINE:
                $score_difference = $event->candidate1_score - $event->candidate2_score;
                if ($score_difference > 0) {
                    return Vote::$VOTE_CASE_WIN;
                } elseif ($score_difference < 0) {
                    return Vote::$VOTE_CASE_LOSS;
                } else {
                    return Vote::$VOTE_CASE_TIE;
                }
                break;
            default:
                break;
        }
        return false;
    }

    public function getVoteCountAttribute()
    {
        return $this->win_vote_count + $this->loss_vote_count +
            $this->tie_vote_count;
    }

    public function getWinVotePercentAttribute()
    {
        $total = $this->vote_count;
        if ($total === 0) {
            return 0;
        }
        return round(($this->win_vote_count / $total) * 100, 2);
    }

    public function getLossVotePercentAttribute()
    {
        $total = $this->vote_count;
        if ($total === 0) {
            return 0;
        }
        return 100 - $this->win_vote_percent - $this->tie_vote_percent;
    }

    public function getTieVotePercentAttribute()
    {
        $total = $this->vote_count;
        if ($total === 0) {
            return 0;
        }
        return round(($this->tie_vote_count / $total) * 100, 2);
    }
}
