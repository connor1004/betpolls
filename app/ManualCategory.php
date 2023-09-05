<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManualCategory extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'name_es', 'slug', 'slug_es', 'display_order',
        'title', 'title_es', 'meta_keywords', 'meta_keywords_es',
        'meta_description', 'meta_description_es',
        'content', 'content_es'
    ];

    protected $appends = [
        'url', 'url_es'
    ];

    public static function getNextDisplayOrder()
    {
        return ManualCategory::withTrashed()->max('display_order') + 1;
    }

    public function getUrlAttribute()
    {
        return url("future/{$this->slug}");
    }

    public function getUrlEsAttribute()
    {
        return url("es/futuro/{$this->slug_es}");
    }

    public function getLocaleUrl($is_future) {
        $locale = app('translator')->getLocale();
        $urlStr = $locale == 'es' ? 'es/' : '';
        if ($is_future) {
            $urlStr .= ($locale == 'es' ? 'futuros/' : 'futures/');
        }
        else {
            $urlStr .= ($locale == 'es' ? 'deporte/' : 'sport/');
        }
        $urlStr .= ($locale == 'es' ? $this->slug_es : $this->slug);
        return url($urlStr);
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
