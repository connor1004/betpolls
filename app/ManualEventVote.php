<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManualEventVote extends Model
{
    public static $VOTE_CASE_WIN = 'win';
    public static $VOTE_CASE_LOSS = 'loss';
    public static $VOTE_CASE_TIE = 'tie';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'page_id', 'event_id', 'bet_type_id', 'user_id', 'event_bet_type_id', 'vote_case', 'score', 'matched',
        'calculated', 'calculated_at'
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

    public function event_bet_type()
    {
        return $this->belongsTo('App\ManualEventBetType');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
