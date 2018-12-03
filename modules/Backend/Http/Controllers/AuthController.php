<?php

namespace Modules\Backend\Http\Controllers;


use Illuminate\Http\Request;
use Modules\Common\Exception\LogicException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\User;
use Carbon\Carbon;
use Modules\Common\SystemLogger;


class AuthController extends BaseController
{
    /**
     * postLogin
     * 登录
     *
     * @param Request $request
     * @return array
     * @throws LogicException
     */
    public function postLogin(Request $request)
    {
        // 凭证
        $username = $request->input('username');
        $password = $request->input('password');
        $user = User::where('username', $username)->first();
        if ($user && Hash::check($password, $user->password)) {
            if ($user->is_locked == 1) {
                throw new LogicException(LogicException::USER_HAVE_BEEN_LOCKED);
            } else {
                $ttl = config('third-party.api.login_token.ttl', 7200);
                $prefix = config('third-party.api.login_token.prefix', 'api_token:');
                $expiredAt = time() + $ttl;
                $expiredTime = Carbon::now()->addSeconds($ttl + 60);
                $token = base64_encode(str_random(40));
                $key = $prefix.''.$token;
                Cache::put($key, 'uid:'.$user->id, $expiredTime);
                SystemLogger::write([
                    'user_id' => $user->id,
                    'type' => 'session',
                    'url' => $request->url(),
                    'operator_ip' => $request->ip(),
                    'content' => '[API_LOGIN]username:'.$user->username,
                ]);
                return [
                    'access_token' => $token,
                    'uid' => $user->id,
                    'username' => $user->username,
                    'scope' => 'administrator',
                    'expired_at' => $expiredAt,
                ];
            }
        } else {
            // 登录失败
            throw new LogicException(LogicException::USER_LOGIN_FAIL);
        }
    }
}