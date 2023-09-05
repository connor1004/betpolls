<?php
namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\User;

class AuthController extends Controller
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

    public function register(Request $request)
    {
        $this->validate($request, [
            'firstname' => 'required',
            'lastname' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        $firstname = $request->input('firstname');
        $lastname = $request->input('lastname');
        $username = $request->input('username');
        $email = $request->input('email');
        $password = Hash::make($request->input('password'));
        $user = User::create([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'username' => $username,
            'email' => $email,
            'password' => $password,
        ]);

        return $this->login($request);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);
        $username = $request->input('username');
        $user = User::where(function ($query) use ($username) {
            $query->where('username', $username)->orWhere('email', $username);
        })->where('role', 'admin')->first();
        if ($user) {
            if (Hash::check($request->input('password'), $user->password)) {
                $key = env('APP_KEY');
                $token = [
                    'exp' => time() + 60 * 60 * 24 * 365,
                    'data' => [
                        'id' => $user->id,
                    ]
                ];
                $jwt = JWT::encode($token, $key);
                return response()->json([
                    'token' => $jwt,
                    'user' => $user
                ]);
            }
        }
        return response()->json([
            'username' => [trans('app.username_or_password_is_invalid')]
        ], 422);
    }
}
