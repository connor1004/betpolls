<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\User;
use Firebase\JWT\JWT;

class GeneralUserController extends AdminController
{
    public function toggleActive($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        if ($user->trashed()) {
            $user->restore();
        } else {
            $user->delete();
        }

        return $user;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $inactive = filter_var($request->input('inactive', false), FILTER_VALIDATE_BOOLEAN);
        $confirmed = $request->input('confirmed', '');
        $latest = filter_var($request->input('latest', false), FILTER_VALIDATE_BOOLEAN);

        $users = User::where(function ($query) use ($search) {
            $query
                ->orWhere('firstname', 'LIKE', "%$search%")
                ->orWhere('lastname', 'LIKE', "%$search%")
                ->orWhere('username', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%")
                ->orWhere('created_at', 'LIKE', "%$search%");
        });
        if ($confirmed !== '') {
            $users->where('confirmed', $confirmed);
        }
        if ($latest) {
            $users->orderBy('created_at', 'DESC');
        }
        if ($inactive) {
            $users->onlyTrashed();
        }

        return $users->paginate(10);
    }

    public function show($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        return $user;
    }

    public function store(Request $request)
    {
        $data = $request->only(
            'firstname', 'lastname', 'username',
            'email', 'password', 'country',
            'role', 'robot'
        );
        $this->validate($request, [
            'firstname' => 'required',
            'lastname' => 'required',
            'username' => 'required|alpha_num|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'country' => 'required',
            'role' => 'required'
        ]);
        $data['password'] = Hash::make($request->input('password'));
        $user = User::create($data);
        return $user;
    }

    public function change_password(Request $request) {
        $this->validate($request, [
            'password' => 'required',
            'new_password' => 'required',
            'user_id' => 'required',
        ]);
        $user = User::find($request->input('user_id'));
        if (!$user) {
            return response()->json([
                'password' => [trans('app.must_be_authenticated')]
            ], 422);
        }
        if (Hash::check($request->input('password'), $user->password)) {
            $user->password = Hash::make($request->input('new_password'));
            $user->save();
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
        return response()->json([
            'password' => [trans('app.old_password_is_invalid')]
        ], 422);
    }

    public function update(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->validate($request, [
            'username' => "alpha_num|unique:users,username,{$user->id}",
            'email' => "email|unique:users,email, {$user->id}"
        ]);
        $user->fill($request->all());
        $user->save();
        return $user;
    }

    public function destroy($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $deleted = $user->forceDelete();
        return $user;
    }
}
