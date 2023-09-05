<?php

namespace App\Http\Controllers\Front;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Facades\Geoip;

class Controller extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        // setlocale(LC_TIME, $geoip->timezone);
    }
}
