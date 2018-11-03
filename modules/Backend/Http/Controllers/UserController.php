<?php

namespace Modules\Backend\Http\Controllers;


use Illuminate\Http\Request;
use Modules\Common\Exception\LogicException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class UserController extends BaseController
{

    public function getInfo()
    {
        $user = Auth::user();
        return [
            'avatar' => 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif',
            'name' => $user->username,
            'roles' => [
                'admin'
            ],
            'permissions' => [

            ],
        ];
    }

    public function getProfile()
    {
        $user = Auth::user();
        return [
            'username' => $user->username,
        ];
    }
}