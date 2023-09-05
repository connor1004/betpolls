<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeagueTeam extends Model
{
    protected $primaryKey = ['league_id', 'team_id'];
    public $incrementing = false;
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'league_id', 'league_division_id', 'team_id',
    ];

    public function league()
    {
        return $this->belongsTo('App\League');
    }

    public function league_division()
    {
        return $this->belongsTo('App\LeagueDivision');
    }

    public function team()
    {
        return $this->belongsTo('App\Team');
    }
}
