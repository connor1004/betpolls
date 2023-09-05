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
use App\Post;
use App\Mails\ConfirmationMail;
use App\Mails\ResetPasswordMail;

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
        $this->middleware('redirectIfAuthenticated');
    }

    public function registerGet()
    {
        $page = Post::where('slug', 'register')->where('post_type', Post::$POST_TYPE_PAGE)->first();
        return view('front.register', [
            'page' => $page
        ]);
    }

    public function registerPost(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'g-recaptcha-response' => 'required|recaptcha',
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'username' => 'required|alpha_dash|unique:users',
            'email' => 'required|unique:users',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('errors', $validator->errors());
            $request->session()->flash('values', $request->all());
            return redirect(Utils::localeUrl('register'));
        }

        $data['password'] = Hash::make($request->input('password'));
        $user = User::create($data);
        Mail::to($user)->send(new ConfirmationMail($user));
        if ($user) {
            return $this->loginPost($request);
        }
        $page = Post::where('slug', 'register')->where('post_type', Post::$POST_TYPE_PAGE)->first();
        return view('front.register', [
            'alert' => [
                'status' => 'danger',
                'message' => trans('app.unable_to_process_your_request')
            ],
            'page' => $page
        ]);
    }

    public function loginGet()
    {
        $page = Post::where('slug', 'login')->where('post_type', Post::$POST_TYPE_PAGE)->first();
        return view('front.login', [
            'page' => $page
        ]);
    }

    public function loginPost(Request $request)
    {
        $data = $request->only('username', 'password');
        $validator = Validator::make($data, [
            'username' => 'required',
            'password' => 'required|min:6'
        ]);
        if ($validator->fails()) {
            $request->session()->flash('errors', $validator->errors());
            $request->session()->flash('values', $request->all());
            return redirect(Utils::localeUrl('login'));
        }

        $username = $request->input('username');
        $user = User::where(function ($query) use ($username) {
            $query->where('username', $username)->orWhere('email', $username);
        })->first();
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
                $tokenCookie = new Cookie('token', $jwt, time() + 60 * 60 * 24 * 365);
                $redirect = $request->get('redirect', Utils::localeUrl('profile'));
                return response(view('front.redirect', [
                    'url' => $redirect
                ]))->header('Set-Cookie', $tokenCookie->__toString());
            }
        }
        $request->session()->flash('alert', (object)([
            'status' => 'danger',
            'message' => trans('app.invalid_username_or_password')
        ]));
        $request->session()->flash('values', $request->all());
        return redirect(Utils::localeUrl('login'));
    }

    public function forgotPasswordGet()
    {
        return view('front.forgot');
    }

    public function forgotPasswordPost(Request $request)
    {
        $data = $request->only('email');
        $validator = Validator::make($data, [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            $request->session()->flash('errors', $validator->errors());
            $request->session()->flash('values', $request->all());
            return redirect(Utils::localeUrl('forgot'));
        }

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if ($user === null) {
            $request->session()->flash('alert', (object)([
                'status' => 'danger',
                'message' => trans('app.unable_to_process_your_request')
            ]));
            $request->session()->flash('values', $request->all());
            return redirect(Utils::localeUrl('forgot'));
        }

        Mail::to($user)->send(new ResetPasswordMail($user));

        $request->session()->flash('alert', (object)([
            'status' => 'success',
            'message' => trans('app.we_have_sent_you_an_email_with_reset_instructions')
        ]));
        $request->session()->flash('values', $request->all());
        return redirect(Utils::localeUrl('forgot'));
    }

    public function resetPasswordGet(Request $request)
    {
        $code = $request->input('code', '');
        $user = User::findByCode($code);
        if (!$user) {
            return view('front.reset', [
                'alert' => (object)([
                    'status' => 'danger',
                    'message' => trans('app.the_reset_request_is_invalid_or_expired')
                ])
            ]);
        }
        return view('front.reset', [
            'user' => $user
        ]);
    }

    public function resetPasswordPost(Request $request)
    {
        $code = $request->input('code', '');
        $user = User::findByCode($code);
        if (!$user) {
            return view('front.reset', [
                'alert' => (object)([
                    'status' => 'danger',
                    'message' => trans('app.the_reset_request_is_invalid_or_expired')
                ])
            ]);
        }
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return view('front.reset', [
                'errors' => $validator->errors(),
            ]);
        }

        if ($user->update(['password' => Hash::make($request->input('password'))])) {
            return redirect(Utils::localeUrl('login'));
        }
        return view('front.reset', [
            'user' => $user,
            'alert' => (object)([
                'status' => 'danger',
                'message' => trans('app.unable_to_process_your_request')
            ])
        ]);
    }
}
