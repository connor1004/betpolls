<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Facades\Utils;

class Game extends Model
{
    public static $STATUS_NOT_STARTED = 'not_started';
    public static $STATUS_POSTPONED = 'postponed';
    public static $STATUS_STARTED = 'started';
    public static $STATUS_ENDED = 'ended';

    public static $IMPORT_SATUS_NOT_STARTED = 75;
    public static $IMPORT_STATUS_STARTED = 100;
    public static $IMPORT_STATUS_ENDED = 50;
    public static $IMPORT_STATUS_CANCELLED = 5;
    public static $IMPORT_STATUS_SUSPENDED = 4;

    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'ref_id', 'league_id', 'start_at', 'status', 'between_players',
        'home_team_id', 'away_team_id', 'home_team_score', 'away_team_score',
        'voter_count', 'vote_count', 'calculated', 'calculated_at', 'calculating_at',
        'meta', 'meta_es', 'game_info', 'game_general_info', 'stop_voting', 'setting_manually',
        'is_nulled'
    ];

    protected $casts = [
        'meta' => 'array',
        'meta_es' => 'array',
        'game_info' => 'array',
        'game_general_info' => 'array'
    ];

    public function league()
    {
        return $this->belongsTo('App\League');
    }

    public function game_bet_types()
    {
        return $this->hasMany('App\GameBetType');
    }

    public function available_game_bet_types()
    {
        return $this->hasMany(('App\GameBetType'))->where('weight_1', '!=', 0);
    }

    public function home_team()
    {
        return $this->belongsTo('App\Team', 'home_team_id');
    }

    public function away_team()
    {
        return $this->belongsTo('App\Team', 'away_team_id');
    }

    public function first_game_vote()
    {
        return $this->hasOne('App\GameVote');
    }

    public function votes()
    {
        return $this->hasMany('App\Vote');
    }

    public function getLocaleMetaAttribute()
    {
        $locale = app('translator')->getLocale();
        if ($locale === 'es') {
            $meta_es = $this->meta_es;
            if (empty($meta_es)) {
                return $this->meta;
            }
            $meta_es = Utils::arrayRemoveEmpty($meta_es);
            if (!empty($this->meta)) {
                return array_replace_recursive($this->meta, $meta_es);
            }
        }
        return $this->meta;
    }

    public function createBetTypes()
    {
        $bet_types = $this->league->bet_types;
        $bet_type_ids = [];
        foreach ($bet_types as $bet_type) {
            array_push($bet_type_ids, $bet_type->id);
            try {
                GameBetType::create([
                    'bet_type_id' => $bet_type->id,
                    'game_id' => $this->id
                ]);
            } catch (\Exception $e) {
                //
            }
        }
        GameBetType::where('game_id', $this->id)->whereNotIn('bet_type_id', $bet_type_ids)->delete();
    }
}
