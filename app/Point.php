<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Facades\Constants;
use Log;

class Point extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'sport_category_id', 'league_id', 'position',
        'start_at', 'score', 'type'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function league()
    {
        return $this->belongsTo('App\League');
    }

    public function sport_category()
    {
        return $this->belongsTo('App\SportCategory');
    }

    public static function addScore($leaderboard, $start_at)
    {
        $point = Point::where([
            'user_id' => $leaderboard->user_id,
            'sport_category_id' => $leaderboard->sport_category_id,
            'league_id' => $leaderboard->league_id,
            'type' => $leaderboard->type,
            'start_at' => $start_at
        ])->first();

        if (!$point) {
            $point = Point::create([
                'user_id' => $leaderboard->user_id,
                'sport_category_id' => $leaderboard->sport_category_id,
                'league_id' => $leaderboard->league_id,
                'type' => $leaderboard->type,
                'score' => $leaderboard->point,
                'start_at' => $start_at
            ]);
        } else {
            $point->score += $leaderboard->point;
            $point->save();
        }
    }

    public static function modifyScore($leaderboard, $start_at, $old_point, $new_point) {
        $point = Point::where([
            'user_id' => $leaderboard->user_id,
            'sport_category_id' => $leaderboard->sport_category_id,
            'league_id' => $leaderboard->league_id,
            'type' => $leaderboard->type,
            'start_at' => $start_at
        ])->first();

        if (!$point) {
            $point = Point::create([
                'user_id' => $leaderboard->user_id,
                'sport_category_id' => $leaderboard->sport_category_id,
                'league_id' => $leaderboard->league_id,
                'type' => $leaderboard->type,
                'score' => $new_point,
                'start_at' => $start_at
            ]);
        } else {
            $point->score += $new_point - $old_point;
            $point->save();
        }
    }
}
