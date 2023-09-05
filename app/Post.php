<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    public static $POST_TYPE_POST = 'post';
    public static $POST_TYPE_PAGE = 'page';
    public static $POST_TYPE_CONTACT = 'contact';
    public static $POST_TYPE_HOME = 'home';

    use SoftDeletes;
    protected $fillable = [
        'title', 'title_es',
        'slug', 'slug_es',
        'content', 'content_es',
        'excerpt', 'excerpt_es',
        'meta_keywords', 'meta_keywords_es',
        'meta_description', 'meta_description_es',
        'author_id', 'featured_image', 'post_type'
    ];

    protected $appends = [
        'url', 'url_es'
    ];

    public function getUrlAttribute()
    {
        return url($this->slug);
    }

    public function getUrlEsAttribute()
    {
        return url("es/{$this->slug_es}");
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
        }
        return $this->title;
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

    public function getLocaleExcerptAttribute()
    {
        $locale = app('translator')->getLocale();
        if ($locale === 'es') {
            if (!empty($this->excerpt_es)) {
                return $this->excerpt_es;
            }
        }
        return $this->excerpt;
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
}
