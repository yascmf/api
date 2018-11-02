<?php

namespace Modules\Backend\Http\Controllers;


use Illuminate\Http\Request;
use Modules\Common\Exception\LogicException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\User;
use Carbon\Carbon;


class AuthController extends BaseController
{
    /**
     * postLogin
     * 登录
     *
     * @return array
     * @throws LogicException
     */
    public function postLogin(Request $request)
    {
        // 认证凭证
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
                return [
                    'access_token' => $token,
                    'uid' => $user->id,
                    'username' => $user->username,
                    'scope' => 'administrator',
                    'expired_at' => $expiredAt,
                ];
            }
        } else {
            // 登录失败，跳回
            throw new LogicException(LogicException::USER_LOGIN_FAIL);
        }
    }
}