<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Geoip extends Model
{
    protected $primaryKey = 'ip';
    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ip', 'continent_code', 'continent_name', 'country_code2', 'country_code3', 'country_name', 'country_capital',
        'state_prov', 'district', 'city', 'zipcode',
        'latitude', 'longitude', 'calling_code', 'country_tld', 'languages', 'country_flag',
        'isp', 'connection_type', 'organization', 'geoname_id', 'currency', 'time_zone'
    ];
}
