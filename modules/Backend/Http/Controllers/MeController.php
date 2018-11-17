<?php

namespace Modules\Backend\Http\Controllers;


use Illuminate\Http\Request;
use Modules\Common\Exception\LogicException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\User;
use Modules\Common\SystemLogger;


class MeController extends BaseController
{

    /**
     * 获取当前用户资料
     */
    public function getProfile()
    {
        $user = Auth::user();
        // 保持一个管理员只有一个角色
        $role =  $user->roles->first();
        $cans = [];
        $perms = $role->perms;
        foreach ($perms as $can) {
            $cans[] = $can->name;
        }
        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'nickname' => $user->nickname,
            'realname' => $user->realname,
            'phone' => $user->phone,
            'avatar' => url('13607832.png'),
            'role' => (null !== $role) ? $role->name : '',
            'cans' => $cans,
        ];
    }

    /**
     * 更新当前用户资料
     */
    public function postProfile(Request $request)
    {
        $user = Auth::user();
        $inputs = $request->only(['nickname', 'realname', 'phone', 'password', 'password_confirmation']);
        $manager = User::find($user->id);
        $rules = $manager->rules(['id' => $user->id]);
        $messages = $manager->messages();
        $validator = Validator::make($inputs, $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages()->first();
            throw new LogicException(LogicException::COMMON_VALIDATION_FAIL, $messages);
        }
        $manager->nickname = $inputs['nickname'];
        $manager->realname = $inputs['realname'];
        $manager->phone = $inputs['phone'];
        if (isset($inputs['password']) && !empty(trim($inputs['password']))) {
            $manager->password = Hash::make($inputs['password']);
        }
        if ($manager->save()) {
            return [
                'code' => LogicException::COMMON_SUCCESS,
                'message' => 'ok',
            ];
        } else {
            throw new LogicException(LogicException::COMMON_DB_SAVE_FAIL);
        }
    }

    /**
     * 登出
     */
    public function postLogout(Request $request)
    {

        $token = $request->bearerToken();
        if ($token) {
            SystemLogger::write([
                'user_id' => Auth::guest() ? '0' : Auth::user()->id,
                'type' => 'session',
                'url' => $request->url(),
                'operator_ip' => $request->ip(),
                'content' => '[API_LOGOUT]access_token:'.$token,
            ]);
            $key = config('third-party.api.login_token.prefix', 'api_token:') . $token;
            Cache::forget($key);

        }
        return [
            'code' => LogicException::COMMON_SUCCESS,
            'message' => 'ok',
        ];
    }
}