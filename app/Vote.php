<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
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
        'id', 'game_bet_type_id', 'game_id', 'bet_type_id', 'user_id', 'vote_case', 'score', 'matched',
        'calculated', 'calculated_at'
    ];

    public function game_bet_type()
    {
        return $this->belongsTo('App\GameBetType');
    }

    public function game()
    {
        return $this->belongsTo('App\Game');
    }

    public function bet_type()
    {
        return $this->belongsTo('App\BetType');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
