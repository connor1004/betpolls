<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Log;

class GameBetType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'bet_type_id', 'game_id', 'weight_1', 'weight_2', 'weight_3', 'weight_4', 'weight_5',
    ];

    public function game()
    {
        return $this->belongsTo('App\Game');
    }

    public function bet_type()
    {
        return $this->belongsTo('App\BetType');
    }

    public function game_vote()
    {
        return $this->hasOne('App\GameVote');
    }

    public function getMatchCaseAttribute()
    {
        $game = $this->game;
        $bet_type = $this->bet_type;

        if ($game->status !== Game::$STATUS_ENDED && $game->status !== Game::$STATUS_STARTED) {
            return false;
        }

        $home_score = 0;
        $away_score = 0;
        if ($game->between_players && ($bet_type->value == BetType::$VALUE_SPREAD || $bet_type->value == BetType::$VALUE_OVER_UNDER)) {
            if (!$game->game_info || !$game->game_info['hometeam'] || !$game->game_info['awayteam']) {
                return false;
            }
            $scores = [];
            $scores[] = $game->game_info['hometeam']['score'];
            $scores[] = $game->game_info['awayteam']['score'];
            $score_sums = [0, 0];
            $i = 0;
            foreach ($scores as $score) {
                foreach ($score as $score_element) {
                    $score_sums[$i] += (int)($score_element);
                }
                $i ++;
            }
            $home_score = $score_sums[0];
            $away_score = $score_sums[1];
        }

        switch ($bet_type->value) {
            case BetType::$VALUE_SPREAD:
                if ($game->between_players) {
                    $score_difference = $home_score - $away_score;
                }
                else {
                    $score_difference = $game->home_team_score - $game->away_team_score;
                }
                if ($score_difference > -$this->weight_1) {
                    return Vote::$VOTE_CASE_WIN;
                } elseif ($score_difference < -$this->weight_1) {
                    return Vote::$VOTE_CASE_LOSS;
                } else {
                    return false;
                }
                break;
            case BetType::$VALUE_OVER_UNDER:
                if ($game->between_players) {
                    $score_total = $home_score + $away_score;
                }
                else {
                    $score_total = $game->home_team_score + $game->away_team_score;
                }
                if ($score_total > $this->weight_1) {
                    return Vote::$VOTE_CASE_WIN;
                } elseif ($score_total < $this->weight_1) {
                    return Vote::$VOTE_CASE_LOSS;
                } else {
                    return false;
                }
                break;
            case BetType::$VALUE_MONEYLINE:
                $score_difference = $game->home_team_score - $game->away_team_score;
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
}
