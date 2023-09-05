<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Facades\Utils;

class ManualFuture extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'page_id', 'name', 'name_es', 'voter_count',
        'display_order', 'meta', 'meta_es', 'published',
        'calculated', 'calculated_at', 'calculating_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'meta_es' => 'array',
    ];

    public static function getNextDisplayOrder()
    {
        return ManualFuture::withTrashed()->max('display_order') + 1;
    }

    public function page()
    {
        return $this->belongsTo('App\ManualPollPage');
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
        }
        return $this->meta;
    }
}
