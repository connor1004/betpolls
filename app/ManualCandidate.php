<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Facades\Utils;

class ManualCandidate extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'category_id', 'country_id', 'logo',
        'candidate_type_id', 'name', 'name_es',
        'short_name', 'short_name_es', 'slug', 'slug_es',
        'title', 'title_es', 'meta_keywords', 'meta_keywords_es',
        'meta_description', 'meta_description_es', 'meta', 'meta_es',
    ];

    protected $casts = [
        'meta' => 'array',
        'meta_es' => 'array'
    ];

    public function category()
    {
        return $this->belongsTo('App\ManualCategory');
    }

    public function country()
    {
        return $this->belongsTo('App\ManualCountry');
    }

    public function candidate_type()
    {
        return $this->belongsTo('App\ManualCandidateType');
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
        return $this->short_name ? $this->short_name : $this->name;
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

    public function getLogoUrlAttribute() {
        if ($this->logo) {
            return $this->logo;
        } else if ($this->country) {
            return $this->country->logo;
        } else {
            return '';
        }
    }
}
