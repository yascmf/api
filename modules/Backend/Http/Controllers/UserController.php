<?php

namespace Modules\Backend\Http\Controllers;


use Illuminate\Http\Request;
use Modules\Common\Exception\LogicException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class USerController extends BaseController
{
    public function getProfile()
    {
        $user = Auth::user();
        return [
            'username' => $user->username,
        ];
    }
}