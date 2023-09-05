<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Facades\Utils;

class ManualPollPage extends Model
{
    public static $STATUS_NOT_STARTED = 'not_started';
    public static $STATUS_POSTPONED = 'postponed';
    public static $STATUS_STARTED = 'started';
    public static $STATUS_ENDED = 'ended';

    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'category_id', 'subcategory_id', 'start_at', 'status',
        'name', 'name_es', 'location', 'location_es', 'logo', 'show_scores',
        'home_top_picks', 'is_future', 'meta', 'meta_es', 'slug', 'slug_es',
        'calculated', 'calculated_at', 'calculating_at', 'published'
    ];

    protected $appends = [
        'url', 'url_es', 'title', 'meta_keywords', 'meta_description'
    ];

    protected $casts = [
        'meta' => 'array',
        'meta_es' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo('App\ManualCategory');
    }

    public function subcategory() {
        return $this->belongsTo('App\ManualSubcategory');
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

    public function getLocaleLocationAttribute()
    {
        $locale = app('translator')->getLocale();
        if ($locale === 'es') {
            if (!empty($this->location_es)) {
                return $this->location_es;
            }
        }
        return $this->location;
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
            else {
                return $this->meta_es;
            }
        }
        return $this->meta;
    }

    public function getUrlAttribute() {
        if (!$this->category) {
            return '';
        }
        $urlStr = $this->is_future ? 'futures/' : 'sport/';
        $urlStr .= "{$this->category->slug}/";
        if ($this->subcategory) {
            $urlStr .= "{$this->subcategory->slug}/";
        }
        $urlStr .= $this->slug;
        return url($urlStr);
    }

    public function getUrlEsAttribute() {
        if (!$this->category) {
            return '';
        }
        $urlStr = 'es/'.($this->is_future ? 'futuros/' : 'deporte/');

        $urlStr .= "{$this->category->slug_es}/";
        if ($this->subcategory) {
            $urlStr .= "{$this->subcategory->slug_es}/";
        }
        $urlStr .= $this->slug_es;
        return url($urlStr);
    }

    public function getLocaleUrlAttribute() {
        $locale = app('translator')->getLocale();
        if ($locale == 'es') {
            return $this->url_es;
        }
        return $this->url;
    }

    public function getLocaleTitleAttribute()
    {
        if (!empty($this->locale_meta) && !empty($this->locale_meta['title'])) {
            return $this->locale_meta['title'];
        } else {
            return $this->locale_name;
        }
    }

    public function getTitleAttribute()
    {
        if (!empty($this->meta) && !empty($this->meta['title'])) {
            return $this->meta['title'];
        } else {
            return '';
        }
    }

    public function getLocaleMetaKeywordsAttribute()
    {
        if (!empty($this->locale_meta) && !empty($this->locale_meta['keywords'])) {
            return $this->locale_meta['keywords'];
        } else {
            return '';
        }
    }

    public function getMetaKeywordsAttribute()
    {
        if (!empty($this->meta) && !empty($this->meta['keywords'])) {
            return $this->meta['keywords'];
        } else {
            return '';
        }
    }

    public function getLocaleMetaDescriptionAttribute()
    {
        if (!empty($this->locale_meta) && !empty($this->locale_meta['description'])) {
            return $this->locale_meta['description'];
        } else {
            return '';
        }
    }

    public function getMetaDescriptionAttribute()
    {
        if (!empty($this->meta) && !empty($this->meta['description'])) {
            return $this->meta['description'];
        } else {
            return '';
        }
    }

    public function getLocaleContentAttribute()
    {
        if (!empty($this->locale_meta) && !empty($this->locale_meta['content'])) {
            return $this->locale_meta['content'];
        } else {
            return '';
        }
    }

    public function getContentAttribute()
    {
        if (!empty($this->meta) && !empty($this->meta['content'])) {
            return $this->meta['content'];
        } else {
            return '';
        }
    }
}
