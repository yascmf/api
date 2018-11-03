<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Modules\Common\Exception\LogicException;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        /*
        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->input('api_token')) {
                return User::where('api_token', $request->input('api_token'))->first();
            }
        });
        */
        
        $this->app['auth']->viaRequest('api', function ($request) {
            $token = $request->bearerToken();
            if ($token) {
                $key = config('third-party.api.login_token.prefix', 'api_token:').$token;
                if ($cacheStr = Cache::get($key)) {
                    list($type, $id) = explode(':', $cacheStr);
                    if ($type == 'uid') {  // 后台管理型用户
                        $user = User::find($id);
                        if ($user) {
                            return $user;
                        } else {
                            throw new LogicException(LogicException::USER_NOT_FOUND);
                        }
                    } elseif ($type == 'mid') {  // 前台会员

                    }
                } else {
                    throw new LogicException(LogicException::USER_NEED_LOGIN);
                }
            }
        });
        
    }
}
