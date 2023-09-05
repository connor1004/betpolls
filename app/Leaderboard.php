<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Leaderboard extends Model
{
    public static $PERIOD_TYPE_WEEKLY = 'weekly';
    public static $PERIOD_TYPE_MONTHLY = 'monthly';
    public static $PERIOD_TYPE_YEARLY = 'yearly';
    public static $PERIOD_TYPE_FOREVER = 'forever';
    public static $PERIOD_TYPE_7_DAYS = '7';
    public static $PERIOD_TYPE_30_DAYS = '30';
    public static $PERIOD_TYPE_365_DAYS = '365';
    public static $PERIOD_TYPE_RANKING = 'ranking';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'sport_category_id', 'league_id', 'start_at', 'period_type', 'score',
        'vote_count', 'calculated_vote_count', 'matched_vote_count', 'type', 'position', 'point'
    ];

    public function sport_category()
    {
        return $this->belongsTo('App\SportCategory');
    }

    public function bet_types()
    {
        return $this->belongsToMany('App\BetType', 'league_bet_type');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public static function getNextDisplayOrder()
    {
        return League::withTrashed()->max('display_order') + 1;
    }

    public function getUrlAttribute()
    {
        return url("{$this->sport_category->slug}/{$this->slug}");
    }

    public function getMatchPercentageAttribute()
    {
        if ($this->calculated_vote_count === 0) {
            return 0;
        }
        return round($this->matched_vote_count / $this->calculated_vote_count * 100, 2);
    }

    public function matchPercentageBadge()
    {
        $percentage = $this->matchPercentage;
        if ($percentage < 45) {
            $className = 'badge-danger';
        } elseif ($percentage < 55) {
            $className = 'badge-warning';
        } else {
            $className = 'badge-success';
        }

        return "<span class=\"badge {$className}\">{$percentage}%</span>";
    }

    public static function addVoteCount($game, $vote)
    {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at);
        $sport_category_id = $game->league->sport_category->id;
        $league_id = $game->league->id;
        $user_id = $vote->user_id;

        $period_types = [
            self::$PERIOD_TYPE_WEEKLY => (new Carbon($date))->setTimezone('America/New_York')->startOfWeek()->format('Y-m-d'),
            self::$PERIOD_TYPE_MONTHLY => (new Carbon($date))->setTimezone('America/New_York')->startOfMonth()->format('Y-m-d'),
            self::$PERIOD_TYPE_YEARLY => (new Carbon($date))->setTimezone('America/New_York')->startOfYear()->format('Y-m-d'),
            self::$PERIOD_TYPE_FOREVER => (new Carbon($date))->setTimezone('America/New_York')->startOfCentury()->format('Y-m-d'),
        ];

        $ids_groups = [
            (object) [
                'type' => 0,
                'sport_category_id' => 0,
                'league_id' => 0
            ],
            (object) [
                'type' => 1,
                'sport_category_id' => 0,
                'league_id' => 0
            ],
            (object) [
                'type' => 1,
                'sport_category_id' => $sport_category_id,
                'league_id' => 0
            ],
            (object) [
                'type' => 1,
                'sport_category_id' => $sport_category_id,
                'league_id' => $league_id
            ]
        ];

        foreach ($period_types as $period_type => $start_at) {
            foreach ($ids_groups as $ids_group) {
                $params = [
                    'type' => $ids_group->type,
                    'sport_category_id' => $ids_group->sport_category_id,
                    'league_id' => $ids_group->league_id,
                    'user_id' => $user_id,
                    'start_at' => $start_at,
                    'period_type' => $period_type,
                ];
                $leaderboard = Leaderboard::where($params)->first();
                if ($leaderboard) {
                    $leaderboard->vote_count++;
                    $leaderboard->save();
                } else {
                    $params['vote_count'] = 1;
                    $leaderboard = Leaderboard::create($params);
                }
            }
        }
    }

    public static function addScore($game, $vote)
    {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at);
        $sport_category_id = $game->league->sport_category->id;
        $league_id = $game->league->id;
        $user_id = $vote->user_id;

        $period_types = [
            self::$PERIOD_TYPE_WEEKLY => (new Carbon($date))->setTimezone('America/New_York')->startOfWeek()->format('Y-m-d'),
            self::$PERIOD_TYPE_MONTHLY => (new Carbon($date))->setTimezone('America/New_York')->startOfMonth()->format('Y-m-d'),
            self::$PERIOD_TYPE_YEARLY => (new Carbon($date))->setTimezone('America/New_York')->startOfYear()->format('Y-m-d'),
            self::$PERIOD_TYPE_FOREVER => (new Carbon($date))->setTimezone('America/New_York')->startOfCentury()->format('Y-m-d'),
        ];

        $ids_groups = [
            (object) [
                'type' => 0,
                'sport_category_id' => 0,
                'league_id' => 0
            ],
            (object) [
                'type' => 1,
                'sport_category_id' => 0,
                'league_id' => 0
            ],
            (object) [
                'type' => 1,
                'sport_category_id' => $sport_category_id,
                'league_id' => 0
            ],
            (object) [
                'type' => 1,
                'sport_category_id' => $sport_category_id,
                'league_id' => $league_id
            ]
        ];

        foreach ($period_types as $period_type => $start_at) {
            foreach ($ids_groups as $ids_group) {
                $params = [
                    'type' => $ids_group->type,
                    'sport_category_id' => $ids_group->sport_category_id,
                    'league_id' => $ids_group->league_id,
                    'user_id' => $user_id,
                    'start_at' => $start_at,
                    'period_type' => $period_type,
                ];

                $leaderboard = Leaderboard::where($params)->first();

                if ($leaderboard) {
                    $leaderboard->calculated_vote_count++;
                    $leaderboard->matched_vote_count += ($vote->matched ? 1 : 0);
                    $leaderboard->score += $vote->score;
                    $leaderboard->save();
                } else {
                    $params['vote_count'] = 1;
                    $params['calculated_vote_count'] = 1;
                    $params['matched_vote_count'] = $vote->matched ? 1 : 0;
                    $params['score'] = $vote->score;
                    $leaderboard = Leaderboard::create($params);
                }
            }
        }
    }

    public static function addManualScore($vote)
    {
        $page = $vote->page;
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $vote->calculated_at);
        $category_id = $page->category_id;
        $subcategory_id = $page->subcategory_id;
        $user_id = $vote->user_id;

        $period_types = [
            self::$PERIOD_TYPE_WEEKLY => (new Carbon($date))->setTimezone('America/New_York')->startOfWeek()->format('Y-m-d'),
            self::$PERIOD_TYPE_MONTHLY => (new Carbon($date))->setTimezone('America/New_York')->startOfMonth()->format('Y-m-d'),
            self::$PERIOD_TYPE_YEARLY => (new Carbon($date))->setTimezone('America/New_York')->startOfYear()->format('Y-m-d'),
            self::$PERIOD_TYPE_FOREVER => (new Carbon($date))->setTimezone('America/New_York')->startOfCentury()->format('Y-m-d'),
        ];

        $ids_groups = [
            (object) [
                'type' => 0,
                'sport_category_id' => 0,
                'league_id' => 0
            ],
            (object) [
                'type' => 2,
                'sport_category_id' => 0,
                'league_id' => 0
            ],
            (object) [
                'type' => 2,
                'sport_category_id' => $category_id,
                'league_id' => 0
            ]
        ];

        if ($subcategory_id) {
            $ids_groups[] = (object) [
                'type' => 2,
                'sport_category_id' => $category_id,
                'league_id' => $subcategory_id
            ];
        }

        foreach ($period_types as $period_type => $start_at) {
            foreach ($ids_groups as $ids_group) {
                $params = [
                    'type' => $ids_group->type,
                    'sport_category_id' => $ids_group->sport_category_id,
                    'league_id' => $ids_group->league_id,
                    'user_id' => $user_id,
                    'start_at' => $start_at,
                    'period_type' => $period_type,
                ];

                $leaderboard = Leaderboard::where($params)->first();

                if ($leaderboard) {
                    $leaderboard->vote_count++;
                    $leaderboard->calculated_vote_count++;
                    $leaderboard->matched_vote_count += ($vote->matched ? 1 : 0);
                    $leaderboard->score += $vote->score;
                    $leaderboard->save();
                } else {
                    $params['vote_count'] = 1;
                    $params['calculated_vote_count'] = 1;
                    $params['matched_vote_count'] = $vote->matched ? 1 : 0;
                    $params['score'] = $vote->score;
                    $leaderboard = Leaderboard::create($params);
                }
            }
        }
    }

    public static function subtractScore($game, $vote)
    {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $game->start_at);
        $sport_category_id = $game->league->sport_category->id;
        $league_id = $game->league->id;
        $user_id = $vote->user_id;

        $period_types = [
            self::$PERIOD_TYPE_WEEKLY => (new Carbon($date))->setTimezone('America/New_York')->startOfWeek()->format('Y-m-d'),
            self::$PERIOD_TYPE_MONTHLY => (new Carbon($date))->setTimezone('America/New_York')->startOfMonth()->format('Y-m-d'),
            self::$PERIOD_TYPE_YEARLY => (new Carbon($date))->setTimezone('America/New_York')->startOfYear()->format('Y-m-d'),
            self::$PERIOD_TYPE_FOREVER => (new Carbon($date))->setTimezone('America/New_York')->startOfCentury()->format('Y-m-d'),
        ];

        $ids_groups = [
            (object) [
                'type' => 0,
                'sport_category_id' => 0,
                'league_id' => 0
            ],
            (object) [
                'type' => 1,
                'sport_category_id' => 0,
                'league_id' => 0
            ],
            (object) [
                'type' => 1,
                'sport_category_id' => $sport_category_id,
                'league_id' => 0
            ],
            (object) [
                'type' => 1,
                'sport_category_id' => $sport_category_id,
                'league_id' => $league_id
            ]
        ];

        foreach ($period_types as $period_type => $start_at) {
            foreach ($ids_groups as $ids_group) {
                $params = [
                    'type' => $ids_group->type,
                    'sport_category_id' => $ids_group->sport_category_id,
                    'league_id' => $ids_group->league_id,
                    'user_id' => $user_id,
                    'start_at' => $start_at,
                    'period_type' => $period_type,
                ];

                $leaderboard = Leaderboard::where($params)->first();

                if ($leaderboard) {
                    $leaderboard->calculated_vote_count--;
                    $leaderboard->matched_vote_count -= ($vote->matched ? 1 : 0);
                    $leaderboard->score -= $vote->score;
                    $leaderboard->save();
                }
            }
        }
    }

    public static function subtractManualScore($vote)
    {
        $page = $vote->page;
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $vote->calculated_at);
        $category_id = $page->category_id;
        $subcategory_id = $page->subcategory_id;
        $user_id = $vote->user_id;

        $period_types = [
            self::$PERIOD_TYPE_WEEKLY => (new Carbon($date))->setTimezone('America/New_York')->startOfWeek()->format('Y-m-d'),
            self::$PERIOD_TYPE_MONTHLY => (new Carbon($date))->setTimezone('America/New_York')->startOfMonth()->format('Y-m-d'),
            self::$PERIOD_TYPE_YEARLY => (new Carbon($date))->setTimezone('America/New_York')->startOfYear()->format('Y-m-d'),
            self::$PERIOD_TYPE_FOREVER => (new Carbon($date))->setTimezone('America/New_York')->startOfCentury()->format('Y-m-d'),
        ];

        $ids_groups = [
            (object) [
                'type' => 0,
                'sport_category_id' => 0,
                'league_id' => 0
            ],
            (object) [
                'type' => 2,
                'sport_category_id' => 0,
                'league_id' => 0
            ],
            (object) [
                'type' => 2,
                'sport_category_id' => $category_id,
                'league_id' => 0
            ]
        ];

        if ($subcategory_id) {
            $ids_groups[] = (object) [
                'type' => 2,
                'sport_category_id' => $category_id,
                'league_id' => $subcategory_id
            ];
        }

        foreach ($period_types as $period_type => $start_at) {
            foreach ($ids_groups as $ids_group) {
                $params = [
                    'type' => $ids_group->type,
                    'sport_category_id' => $ids_group->sport_category_id,
                    'league_id' => $ids_group->league_id,
                    'user_id' => $user_id,
                    'start_at' => $start_at,
                    'period_type' => $period_type,
                ];

                $leaderboard = Leaderboard::where($params)->first();

                if ($leaderboard) {
                    $leaderboard->vote_count--;
                    $leaderboard->calculated_vote_count--;
                    $leaderboard->matched_vote_count -= ($vote->matched ? 1 : 0);
                    $leaderboard->score -= $vote->score;
                    $leaderboard->save();
                }
            }
        }
    }
}
