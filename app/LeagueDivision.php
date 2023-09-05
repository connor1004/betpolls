<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeagueDivision extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'parent_id', 'league_id', 'position', 'name', 'name_es', 'logo', 'display_order',
    ];

    protected $appends = [
        'full_name',
    ];

    public function league()
    {
        return $this->belongsTo('App\League');
    }

    public function parent()
    {
        return LeagueDivision::where('league_id', $this->parent_id)->first();
    }

    public function getFullNameAttribute()
    {
        $name = $this->name;
        $league = LeagueDivision::find($this->parent_id);
        while ($league) {
            $name = "{$league->name}»{$name}";
            $league = LeagueDivision::find($league->parent_id);
        }

        return $name;
    }

    public function getTitleAttribute()
    {
        return $this->name;
    }

    public static function getNextDisplayOrder()
    {
        return LeagueDivision::withTrashed()->max('display_order') + 1;
    }
}
