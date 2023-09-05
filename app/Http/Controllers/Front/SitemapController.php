<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Menu;
use App\User;

class SitemapController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        // $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $menus = Menu::all();
        $users = User::where(
            'role', '!=', User::$ROLE_UNKNOWN
        )->get();
        return response(view('front.sitemap', [
            'menus' => $menus,
            'users' => $users
        ]), 200, [
            'Content-Type' => 'text/xml'
        ]);
    }
}
