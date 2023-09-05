<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Facades\Utils;

class ManualEvent extends Model
{
  use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'page_id', 'name', 'name_es', 'display_order', 'tie_odds',
        'candidate1_id', 'candidate1_score', 'candidate1_standing', 'candidate1_odds',
        'candidate2_id', 'candidate2_score', 'candidate2_standing', 'candidate2_odds',
        'spread', 'over_under', 'over_under_score', 'voter_count', 'vote_count',
        'meta', 'meta_es', 'calculated', 'calculated_at', 'calculating_at',
        'spread_win_points', 'spread_loss_points',
        'moneyline1_win_points', 'moneyline1_loss_points',
        'moneyline2_win_points', 'moneyline2_loss_points',
        'moneyline_tie_win_points', 'moneyline_tie_loss_points',
        'over_under_win_points', 'over_under_loss_points', 'published'
    ];

    protected $casts = [
        'meta' => 'array',
        'meta_es' => 'array'
    ];

    public static function getNextDisplayOrder()
    {
        return ManualEvent::withTrashed()->max('display_order') + 1;
    }

    public function page()
    {
        return $this->belongsTo('App\ManualPollPage');
    }

    public function candidate1()
    {
        return $this->belongsTo('App\ManualCandidate', 'candidate1_id');
    }

    public function candidate2()
    {
        return $this->belongsTo('App\ManualCandidate', 'candidate2_id');
    }

    public function getLocaleNameAttribute()
    {
        $locale = app('translator')->getLocale();
        if ($locale === 'es') {
            if (!empty($this->name_es)) {
                return $this->name_es;
            }
        }
        return $this->name;
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
            return $this->meta_es;
        }
        return $this->meta;
    }
}
