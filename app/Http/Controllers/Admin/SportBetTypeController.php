<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\BetType;

class SportBetTypeController extends AdminController
{
    public function index(Request $request)
    {
        $bet_types = BetType::all();

        return $bet_types;
    }
}
