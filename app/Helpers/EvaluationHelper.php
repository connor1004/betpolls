<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Leaderboard;
use App\Point;
use App\Facades\Constants;
use Log;
use App\Facades\Calculation;

class EvaluationHelper
{
    public function evaluateUserBasedOnLeaderboard($period_type)
    {
        // Evaluate medal
        Log::info('evalutation period_type: '.$period_type);
        $start_at = (new Carbon())->setTimezone('America/New_York');
        switch ($period_type) {
            case Leaderboard::$PERIOD_TYPE_WEEKLY:
                $start_at->subWeeks(1);
                $trophy_start_at = (new Carbon($start_at))->startOfYear()->format('Y-m-d');
                $start_at = $start_at->format('Y-m-d');
                break;
            case Leaderboard::$PERIOD_TYPE_MONTHLY:
                $start_at = $start_at->subMonths(1)->startOfMonth()->format('Y-m-d');
                break;
            case Leaderboard::$PERIOD_TYPE_YEARLY:
            default:
                $start_at = $start_at->subYears(1)->startOfYear()->format('Y-m-d');
                break;
        }

        $leaderboards = Leaderboard::where([
                'start_at' => $start_at,
                'period_type' => $period_type
            ])
            ->orderBy('score', 'DESC')->get();
        
        $current_positions = [];
        foreach ($leaderboards as $leaderboard) {
            $sport_category_id = $leaderboard->sport_category_id;
            $league_id = $leaderboard->league_id;
            $type = $leaderboard->type;

            $position_key = "{$type}_{$sport_category_id}_{$league_id}";

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
            $point = $leaderboard->point == null ? 0 : $leaderboard->point;
            $leaderboard->point = Constants::getPointScore($leaderboard->position);
            $leaderboard->save();

            if ($leaderboard->period_type === Leaderboard::$PERIOD_TYPE_WEEKLY) {
                Point::modifyScore($leaderboard, $trophy_start_at, $point, $leaderboard->point);
            }
        }

        // Evaluate trophy
        if ($period_type !== Leaderboard::$PERIOD_TYPE_WEEKLY) {
            return;
        }

        $current_positions = [];
        $points = Point::where('start_at', $trophy_start_at)->orderBy('score', 'DESC')->get();
        foreach ($points as $point) {
            $sport_category_id = $point->sport_category_id;
            $league_id = $point->league_id;
            $type = $point->type;

            $position_key = "{$type}_{$sport_category_id}_{$league_id}";

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
    }

    public function evaluateUser($period_type)
    {
        // Evaluate medal
        Log::info('evalutation period_type: '.$period_type);
        $start_at = (new Carbon())->setTimezone('America/New_York');
        switch ($period_type) {
            case Leaderboard::$PERIOD_TYPE_WEEKLY:
                $start_at->subWeeks(1);
                $trophy_start_at = (new Carbon($start_at))->startOfYear()->format('Y-m-d');
                $start_at = $start_at->startOfWeek()->format('Y-m-d');
                break;
            case Leaderboard::$PERIOD_TYPE_MONTHLY:
                $start_at = $start_at->subMonths(1)->startOfMonth()->format('Y-m-d');
                break;
            case Leaderboard::$PERIOD_TYPE_YEARLY:
            default:
                $start_at = $start_at->subYears(1)->startOfYear()->format('Y-m-d');
                break;
        }

        $areas = Leaderboard::where([
                'start_at' => $start_at,
                'period_type' => $period_type
            ])
            ->select('type', 'sport_category_id', 'league_id')
            ->distinct()->get();

        foreach ($areas as $area) {
            $leaderboards = Calculation::calculateLeaderboards($area->type, $area->sport_category_id, $area->league_id, $period_type, false);
            
            $current_position = null;
            foreach ($leaderboards as $leaderboard) {
                if (!isset($current_position)) {
                    $current_position = [
                        'position' => 1,
                        'score' => $leaderboard->score
                    ];
                } else {
                    if ($current_position['score'] != $leaderboard->score) {
                        $current_position['score'] = $leaderboard->score;
                        $current_position['position'] += 1;
                    }
                }

                $finder = [
                    'user_id' => $leaderboard->user_id,
                    'type' => $area->type,
                    'sport_category_id' => $area->sport_category_id,
                    'league_id' => $area->league_id,
                    'period_type' => $period_type,
                    'start_at' => $start_at
                ];

                $board = Leaderboard::where($finder)->first();
                $point = $board == null || $board->point == null ? 0 : $board->point;
                $leaderboard->position = $current_position['position'];
                $leaderboard->point = Constants::getPointScore($leaderboard->position);

                $board1 = Leaderboard::updateOrCreate($finder, [
                    'matched_vote_count' => $leaderboard->matched_vote_count,
                    'calculated_vote_count' => $leaderboard->calculated_vote_count,
                    'score' => $leaderboard->score,
                    'position' => $leaderboard->position,
                    'point' => $leaderboard->point
                ]);

                if ($period_type === Leaderboard::$PERIOD_TYPE_WEEKLY) {
                    Point::modifyScore($board1, $trophy_start_at, $point, $board1->point);
                }
            }
        }

        // Evaluate trophy
        if ($period_type !== Leaderboard::$PERIOD_TYPE_WEEKLY) {
            return;
        }

        $current_positions = [];
        $points = Point::where('start_at', $trophy_start_at)->orderBy('score', 'DESC')->get();
        foreach ($points as $point) {
            $sport_category_id = $point->sport_category_id;
            $league_id = $point->league_id;
            $type = $point->type;

            $position_key = "{$type}_{$sport_category_id}_{$league_id}";

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
    }

    public function reevaluate($original_start_at, $modified_start_at) {
        if (!$original_start_at && !$modified_start_at) {
            return;
        }

        $current_week_start = (new Carbon())->setTimezone('America/New_York')->startOfWeek()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $current_month_start = (new Carbon())->setTimezone('America/New_York')->startOfMonth()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $current_year_start = (new Carbon())->setTimezone('America/New_York')->startOfYear()->setTimezone('UTC')->format('Y-m-d H:i:s');
        
        $o_start_at = null;
        $o_week_start = null;
        $o_month_start = null;
        $o_year_start = null;
        $o_trophy_start = null;
        $m_trophy_start = null;
        $periods = array();

        if ($original_start_at != null) {
            $o_start_at = Carbon::createFromFormat('Y-m-d H:i:s', $original_start_at, 'UTC');
            if ($original_start_at < $current_week_start) {
                $o_week_start = (new Carbon($o_start_at))->setTimezone('America/New_York')->startOfWeek()->format('Y-m-d');
                $o_trophy_start = (new Carbon($o_start_at))->setTimezone('America/New_York')->startOfYear()->format('Y-m-d');
                $periods[] = [
                    'type' => Leaderboard::$PERIOD_TYPE_WEEKLY,
                    'start_at' => $o_week_start,
                    'trophy_start_at' => $o_trophy_start,
                    'original' => true
                ];
            }

            if ($original_start_at < $current_month_start) {
                $o_month_start = (new Carbon($o_start_at))->setTimezone('America/New_York')->startOfMonth()->format('Y-m-d');
                $periods[] = [
                    'type' => Leaderboard::$PERIOD_TYPE_MONTHLY,
                    'start_at' => $o_month_start
                ];
            }

            if ($original_start_at < $current_year_start) {
                $o_year_start = (new Carbon($o_start_at))->setTimezone('America/New_York')->startOfYear()->format('Y-m-d');
                $periods[] = [
                    'type' => Leaderboard::$PERIOD_TYPE_YEARLY,
                    'start_at' => $o_year_start
                ];
            }
        }

        if ($modified_start_at != null) {
            $m_start_at = Carbon::createFromFormat('Y-m-d H:i:s', $modified_start_at, 'UTC');
            if ($modified_start_at < $current_week_start) {
                $m_week_start = (new Carbon($m_start_at))->setTimezone('America/New_York')->startOfWeek()->format('Y-m-d');
                $m_trophy_start = (new Carbon($m_start_at))->setTimezone('America/New_York')->startOfYear()->format('Y-m-d');
                if ($o_week_start != $m_week_start) {
                    $periods[] = [
                        'type' => Leaderboard::$PERIOD_TYPE_WEEKLY,
                        'start_at' => $m_week_start,
                        'trophy_start_at' => $m_trophy_start,
                        'original' => false
                    ];
                }
            }

            if ($modified_start_at < $current_month_start) {
                $m_month_start = (new Carbon($m_start_at))->setTimezone('America/New_York')->startOfMonth()->format('Y-m-d');
                if ($o_month_start != $m_month_start) {
                    $periods[] = [
                        'type' => Leaderboard::$PERIOD_TYPE_MONTHLY,
                        'start_at' => $m_month_start
                    ];
                }
            }

            if ($modified_start_at < $current_year_start) {
                $m_year_start = (new Carbon($m_start_at))->setTimezone('America/New_York')->startOfYear()->format('Y-m-d');
                if ($o_year_start != $m_year_start) {
                    $periods[] = [
                        'type' => Leaderboard::$PERIOD_TYPE_YEARLY,
                        'start_at' => $m_year_start
                    ];
                }
            }
        }

        $o_trophy_modified = false;
        $m_trophy_modified = false;
        foreach ($periods as $period) {
            $leaderboards = Leaderboard::where([
                'start_at' => $period['start_at'],
                'period_type' => $period['type']
            ])
            ->orderBy('score', 'DESC')->get();
        
            $current_positions = [];
            foreach ($leaderboards as $leaderboard) {
                $sport_category_id = $leaderboard->sport_category_id;
                $league_id = $leaderboard->league_id;
                $type = $leaderboard->type;
    
                $position_key = "{$type}_{$sport_category_id}_{$league_id}";
    
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
                $point = $leaderboard->point == null ? 0 : $leaderboard->point;
                $leaderboard->point = Constants::getPointScore($leaderboard->position);
                $leaderboard->save();
    
                if ($leaderboard->period_type === Leaderboard::$PERIOD_TYPE_WEEKLY && $point != $leaderboard->point) {
                    Point::modifyScore($leaderboard, $period['trophy_start_at'], $point, $leaderboard->point);
                    if ($period['original']) {
                        $o_trophy_modified = true;
                    }
                    else {
                        $m_trophy_modified = true;
                    }
                }
            }
        }

        if ($o_trophy_modified || $m_trophy_modified) {
            $trophy_starts = [];
            if ($o_trophy_modified) {
                $trophy_starts[] = $o_trophy_start;
            }
            if ($m_trophy_modified) {
                if (!$o_trophy_modified || $o_trophy_start != $m_trophy_start) {
                    $trophy_starts[] = $m_trophy_start;
                }
            }
            foreach ($trophy_starts as $trophy_start) {
                $current_positions = [];
                $points = Point::where('start_at', $trophy_start)->orderBy('score', 'DESC')->get();
                foreach ($points as $point) {
                    $sport_category_id = $point->sport_category_id;
                    $league_id = $point->league_id;
                    $type = $point->type;
        
                    $position_key = "{$type}_{$sport_category_id}_{$league_id}";
        
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
            }
        }
    }
}
