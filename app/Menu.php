<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{

    public static $MENU_TYPE_HEADER = 'header';
    public static $MENU_TYPE_FOOTER = 'footer';
    public static $MENU_TYPE_SOCIAL = 'social';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'parent_id',
        'title', 'title_es',
        'url', 'url_es',
        'sport_category_id', 'league_id', 'post_id',
        'display_order', 'menu_type',
        'top_menu', 'burger_menu', 'icon_url'
    ];

    public function sport_category()
    {
        return $this->belongsTo('App\SportCategory');
    }

    public function league()
    {
        return $this->belongsTo('App\League');
    }

    public function post()
    {
        return $this->belongsTo('App\Post');
    }

    public function parent()
    {
        return $this->belongsTo('App\Menu', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Menu', 'parent_id')->orderBy('display_order');
    }

    public function getMaxDisplayNumber()
    {
        return Menu::max('display_number');
    }

    public function getRealUrlAttribute()
    {
        if (!empty($this->sport_category_id) && $this->sport_category) {
            return $this->sport_category->url;
        }
        if (!empty($this->league_id) && $this->league) {
            return $this->league->url;
        }
        return $this->url;
    }

    public function getRealUrlEsAttribute()
    {
        if (!empty($this->sport_category_id) && $this->sport_category) {
            return $this->sport_category->url_es;
        }
        if (!empty($this->league_id) && $this->league) {
            return $this->league->url_es;
        }
        return $this->url_es;
    }

    public function getLocaleUrlAttribute()
    {
        $locale = app('translator')->getLocale();
        if ($locale === 'es') {
            if (!empty($this->real_url_es)) {
                return $this->real_url_es;
            }
        }
        return $this->real_url;
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
}
