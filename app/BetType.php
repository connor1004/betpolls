<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BetType extends Model
{
    public static $VALUE_SPREAD = 'spread';
    public static $VALUE_MONEYLINE = 'moneyline';
    public static $VALUE_OVER_UNDER = 'over_under';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'value', 'name', 'name_es', 'win_score', 'loss_score', 'tie_score', 'tie_win_score', 'tie_loss_score'
    ];

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
}
