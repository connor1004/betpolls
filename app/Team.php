<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Facades\Utils;

class Team extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'ref_id', 'sport_category_id', 'sport_area_id', 'logo',
        'name', 'name_es',
        'short_name', 'short_name_es',
        'slug', 'slug_es',
        'meta', 'meta_es',
        'title', 'title_es',
        'meta_keywords', 'meta_keywords_es',
        'meta_description', 'meta_description_es',
        'is_player', 'other_info'
    ];

    protected $casts = [
        'meta' => 'array',
        'meta_es' => 'array'
    ];

    public function sport_category()
    {
        return $this->belongsTo('App\SportCategory');
    }

    public function sport_area()
    {
        return $this->belongsTo('App\SportArea');
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

    public function getLocaleShortNameAttribute()
    {
        $locale = app('translator')->getLocale();
        if ($locale === 'es') {
            if (!empty($this->short_name_es)) {
                return $this->short_name_es;
            }
        }
        return $this->short_name;
    }

    public function getLocaleSlugAttribute()
    {
        $locale = app('translator')->getLocale();
        if ($locale === 'es') {
            if (!empty($this->slug_es)) {
                return $this->slug_es;
            }
        }
        return $this->slug;
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
}
