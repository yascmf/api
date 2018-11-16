<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'nickname', 'realname', 'phone',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    // 关联角色
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // 判断用户是否具有 某个或某些 角色
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return !! $role->intersect($this->roles)->count();
    }

    // 判断用户是否具有某权限
    public function hasPermission($permission)
    {
        return $this->hasRole($permission->roles);
    }

    // 给用户分配角色
    public function assignRole($role)
    {
        return $this->roles()->save(
            Role::whereName($role)->firstOrFail()
        );
    }

    // 分配角色
    public function attachRole($role)
    {
        if(is_object($role)) {
            $role = $role->getKey();
        }

        if(is_array($role)) {
            $role = $role['id'];
        }

        $this->roles()->attach($role);
    }

    // 解除角色
    public function detachRole($role)
    {
        if (is_object($role)) {
            $role = $role->getKey();
        }

        if (is_array($role)) {
            $role = $role['id'];
        }

        $this->roles()->detach($role);
    }

    // 分配多个角色
    public function attachRoles($roles)
    {
        foreach ($roles as $role) {
            $this->attachRole($role);
        }
    }

    // 解除多个角色
    public function detachRoles($roles)
    {
        foreach ($roles as $role) {
            $this->detachRole($role);
        }
    }
    
    public function labels()
    {
        return [
            'username' => '用户名',
            'email' => '电子邮箱',
            'password' => '登录密码',
            'nickname' => '昵称',
            'phone' => '电话号码',
            'realname' => '真实姓名',
        ];
    }
    
    public function rules($filters = [])
    {
        if (isset($filters['id'])) {
            // update
            $rules = [
                'nickname'              => 'required|alpha_dash|min:4|max:10',
                'realname'              => 'required|min:2|max:5|regex:/^[\x{4e00}-\x{9fa5}]{2,5}$/u',  //中文正则匹配可能有遗漏
                'password'              => 'min:6|max:16|regex:/^[a-zA-Z0-9~@#%_]{6,16}$/i',  //登录密码只能英文字母(a-zA-Z)、阿拉伯数字(0-9)、特殊符号(~@#%)
                'password_confirmation' => 'same:password',
                'role'                  => 'required|exists:roles,id',
                'is_locked'             => 'required|boolean',
            ];
        } else {
            // store
            $rules = [
                'username'                 => 'required|min:4|max:10|regex:/^[A-Za-z0-9]+$/|unique:users,username',
                'password'                 => 'required|min:6|max:16|regex:/^[a-zA-Z0-9~@#%_]{6,16}$/i',  //登录密码只能英文字母(a-zA-Z)、阿拉伯数字(0-9)、特殊符号(~@#%)
                'password_confirmation'    => 'required|same:password',
                'role'                     => 'required|exists:roles,id',
                'email'                    => 'required|email|unique:users,email',
                'realname'                 => 'required|min:2|max:5|regex:/^[\x{4e00}-\x{9fa5}]{2,5}$/u',  //中文正则匹配可能有遗漏
                'phone'                    => 'size:11|mobile_phone|unique:users,phone',
            ];
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'nickname.required'   => '请填写昵称',
            'nickname.alpha_dash' => '昵称包含特殊字符',
            'nickname.min'        => '昵称过短，长度不得少于4',
            'nickname.max'        => '昵称过长，长度不得超出10',

            'username.unique'        => '此登录名已存在，请尝试其它名字组合',
            'username.required'      => '请填写登录名',
            'username.max'           => '登录名过长，长度不得超出10',
            'username.min'           => '登录名过短，长度不得少于4',
            'username.regex'         => '登录名只能阿拉伯数字与英文字母组合',
            'username.unique'        => '此登录名已存在，请尝试其它名字组合',

            'password.required'              => '请填写登录密码',
            'password.min'                   => '密码长度不得少于6',
            'password.max'                   => '密码长度不得超出16',
            'password.regex'                 => '密码包含非法字符，只能为英文字母(a-zA-Z)、阿拉伯数字(0-9)与特殊符号(~@#%_)组合',
            'password_confirmation.required' => '请填写确认密码',
            'password_confirmation.same'     => '2次密码不一致',

            'role.required' => '请选择角色（用户组）',
            'role.exists'   => '系统不存在该角色（用户组）',

            'email.required' => '请填写邮箱地址',
            'email.email'    => '请填写正确合法的邮箱地址',
            'email.unique'   => '此邮箱地址已存在于系统，不能再进行二次关联',

            'realname.required' => '请填写真实姓名',
            'realname.min'      => '真实姓名字数不得少于2',
            'realname.max'      => '真实姓名字数不得多于5',
            'realname.regex'    => '真实姓名必须为中文',

            'phone.size'         => '国内的手机号码长度为11位',
            'phone.mobile_phone' => '请填写合法的手机号码',
            'phone.unique'       => '此手机号码已存在于系统中，不能再进行二次关联',

            'is_locked.required' => '请选择用户状态',
            'is_locked.boolean'  => '用户状态必须为布尔值',
        ];
    }
}
