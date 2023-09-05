<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameVote extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'game_bet_type_id', 'game_id', 'bet_type_id',
        'win_vote_count', 'loss_vote_count', 'tie_vote_count', 'match_case',
        'calculated',
    ];

    public function game_bet_type()
    {
        return $this->belongsTo('App\GameBetType');
    }

    public function bet_type()
    {
        return $this->belongsTo('App\BetType');
    }

    public function game()
    {
        return $this->belongsTo('App\Game');
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
