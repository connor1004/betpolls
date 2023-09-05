<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Facades\Geoip;
use Carbon\Carbon;
use DB;

class League extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'logo', 'sport_area_id', 'sport_category_id', 'display_order', 'hide_standings',
        'name', 'name_es',
        'slug', 'slug_es',
        'title', 'title_es',
        'meta_keywords', 'meta_keywords_es',
        'meta_description', 'meta_description_es',
        'content', 'content_es'
    ];

    protected $appends = [
        'url', 'url_es'
    ];

    public function sport_category()
    {
        return $this->belongsTo('App\SportCategory');
    }

    public function sport_area()
    {
        return $this->belongsTo('App\SportArea');
    }

    public function bet_types()
    {
        return $this->belongsToMany('App\BetType', 'league_bet_type');
    }

    public function today_games()
    {
        // $geoip = Geoip::getGeoip();
        // $timezone = $geoip ? $geoip->time_zone : 'UTC';
        // $start_at = (new Carbon(null, $timezone))->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        // $end_at = (new Carbon(null, $timezone))->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');

        $start_at = (new Carbon())->setTimezone('America/New_York')->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $end_at = (new Carbon())->setTimezone('America/New_York')->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');

        return $this->hasMany('App\Game', 'league_id')->whereBetween('start_at', [$start_at, $end_at]);
    }

    public static function getNextDisplayOrder()
    {
        return League::withTrashed()->max('display_order') + 1;
    }

    public function getUrlAttribute()
    {
        return !empty($this->sport_category) ? url("sports/{$this->sport_category->slug}/{$this->slug}") : '';
    }

    public function getUrlEsAttribute()
    {
        return !empty($this->sport_category) ? url("es/deportes/{$this->sport_category->slug_es}/{$this->slug_es}") : '';
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

    public function getLocaleUrlAttribute()
    {
        $locale = app('translator')->getLocale();
        if ($locale === 'es') {
            if (!empty($this->url_es)) {
                return $this->url_es;
            }
        }
        return $this->url;
    }

    public function getLocaleTitleAttribute()
    {
        $locale = app('translator')->getLocale();
        if ($locale === 'es') {
            if (!empty($this->title_es)) {
                return $this->title_es;
            }
            if (empty($this->title)) {
                if (!empty($this->name_es)) {
                    return $this->name_es;
                }
            }
        }
        return empty($this->title) ? $this->local_name : $this->title;
    }

    public function getLocaleMetaKeywordsAttribute()
    {
        $locale = app('translator')->getLocale();
        if ($locale === 'es') {
            if (!empty($this->meta_keywords_es)) {
                return $this->meta_keywords_es;
            }
        }
        return $this->meta_keywords;
    }

    public function getLocaleMetaDescriptionAttribute()
    {
        $locale = app('translator')->getLocale();
        if ($locale === 'es') {
            if (!empty($this->meta_description_es)) {
                return $this->meta_description_es;
            }
        }
        return $this->meta_description;
    }

    public function getLocaleContentAttribute()
    {
        $locale = app('translator')->getLocale();
        if ($locale === 'es') {
            if (!empty($this->content_es)) {
                return $this->content_es;
            }
        }
        return $this->content;
    }
}
