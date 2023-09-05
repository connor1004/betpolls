<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\Cookie;

use App\Facades\Utils;
use App\User;
use App\Vote;

class TestController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index() {
        // $votes = Vote::all();
        // foreach($votes as $vote) {
        //     $vote->matched = null;
        //     $vote->save();
        // }
        return null;
    }
}
