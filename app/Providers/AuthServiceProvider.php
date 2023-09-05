<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\ServiceProvider;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Exception;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
    }

    /**
     * Boot the authentication services for the application.
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            $token = $request->cookie('token');
            $key = env('APP_KEY');
            if ($token) {
                try {
                    $credential = JWT::decode($token, $key, ['HS256']);

                    return User::find($credential->data->id);
                } catch (ExpiredException $e) {
                    return null;
                } catch (Exception $e) {
                    return null;
                }
            }
        });
    }
}
