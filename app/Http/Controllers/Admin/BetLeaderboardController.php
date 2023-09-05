<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

use App\Facades\Geoip;
use App\Facades\Calculation;
use App\Facades\Evaluation;
use DB;
use Carbon\Carbon;
use App\Leaderboard;
use App\Point;
use App\Facades\Constants;

class BetLeaderboardController extends AdminController
{
    public function calculateTotal(Request $request)
    {
        Leaderboard::where([
            'type' => 0,
            'sport_category_id' => 0,
            'league_id' => 0
        ])->forceDelete();

        $leaderboards = Leaderboard::where([
            'sport_category_id' => 0,
            'league_id' => 0
        ])->where('type', '>', 0)->get();

        foreach ($leaderboards as $leaderboard) {
            $data = [
                'type' => 0,
                'sport_category_id' => $leaderboard->sport_category_id,
                'league_id' => $leaderboard->league_id,
                'user_id' => $leaderboard->user_id,
                'start_at' => $leaderboard->start_at,
                'period_type' => $leaderboard->period_type
            ];

            $leaderboard0 = Leaderboard::where($data)->first();
            if ($leaderboard0) {
                $leaderboard0->score += $leaderboard->score;
                $leaderboard0->vote_count += $leaderboard->vote_count;
                $leaderboard0->calculated_vote_count += $leaderboard->calculated_vote_count;
                $leaderboard0->matched_vote_count += $leaderboard->matched_vote_count;
                $leaderboard0->save();
            } else {
                $data['score'] = $leaderboard->score;
                $data['vote_count'] = $leaderboard->score;
                $data['calculated_vote_count'] = $leaderboard->score;
                $data['matched_vote_count'] = $leaderboard->matched_vote_count;
                Leaderboard::create($data);
            }
        }

        Point::where([
            'type' => 0,
            'sport_category_id' => 0,
            'league_id' => 0
        ])->forceDelete();

        $leaderboards = Leaderboard::where([
                'type' => 0,
                'sport_category_id' => 0,
                'league_id' => 0
            ])
            ->orderBy('score', 'DESC')->get();
        
        $current_positions = [];
        foreach ($leaderboards as $leaderboard) {
            $sport_category_id = $leaderboard->sport_category_id;
            $league_id = $leaderboard->league_id;
            $type = $leaderboard->type;
            $period_type = $leaderboard->period_type;
            $start_at = $leaderboard->start_at;

            $position_key = "{$type}_{$sport_category_id}_{$league_id}_{$start_at}_{$period_type}";

            if (!isset($current_positions[$position_key])) {
                $current_positions[$position_key] = [
                    'position' => 1,
                    'score' => $leaderboard->score
                ];
                $leaderboard->position = $current_positions[$position_key]['position'];
            } else {
                $current_position = &$current_positions[$position_key];
                if ($current_position['score'] === $leaderboard->score) {
                    $leaderboard->position = $current_position['position'];
                } else {
                    $current_position['position'] = $current_position['position'] + 1;
                    $current_position['score'] = $leaderboard->score;
                    $leaderboard->position = $current_position['position'];
                }
            }

            $leaderboard->point = Constants::getPointScore($leaderboard->position);
            $leaderboard->save();

            if ($leaderboard->period_type === Leaderboard::$PERIOD_TYPE_WEEKLY) {
                $trophy_start_at = (new Carbon($leaderboard->start_at))->startOfYear()->format('Y-m-d');
                Point::addScore($leaderboard, $trophy_start_at);
            }
        }

        $current_positions = [];
        $points = Point::where([
            'type' => 0,
            'sport_category_id' => 0,
            'league_id' => 0
        ])->orderBy('score', 'DESC')->get();
        foreach ($points as $point) {
            $sport_category_id = $point->sport_category_id;
            $league_id = $point->league_id;
            $type = $point->type;
            $start_at = $point->start_at;

            $position_key = "{$type}_{$sport_category_id}_{$league_id}_{$start_at}";

            if (!isset($current_positions[$position_key])) {
                $current_positions[$position_key] = [
                    'position' => 1,
                    'score' => $point->score
                ];
                $point->position = $current_positions[$position_key]['position'];
            } else {
                $current_position = &$current_positions[$position_key];
                if ($current_position['score'] === $point->score) {
                    $point->position = $current_position['position'];
                } else {
                    $current_position['position'] = $current_position['position'] + 1;
                    $current_position['score'] = $point->score;
                    $point->position = $current_position['position'];
                }
            }
            $point->save();
        }

        return [
            'message' => 'Calculated successfully'
        ];
    }
}
