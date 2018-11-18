<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * 建立与 permission 关联关系
     */
    public function perms()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function givePermissionTo($permission)
    {
        return $this->perms()->save($permission);
    }

    public function labels()
    {
        return [
            'name' => '名称',
            'display_name' => '展示名',
            'description' => '描述',
        ];
    }

    public function rules($filters = [])
    {
        $id = isset($filters['id']) && !empty($filters['id']) ? ','.$filters['id'].',id' : '';
        $rules = [
            'name'         => 'required|eng_alpha|min:2|max:20|unique:roles,name'.$id,
            'display_name' => 'required|alpha_dash|min:3|max:40',
            'description'  => 'max:80',
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            'name.required'           => '角色名必须填写',
            'name.max'                => '角色名长度不要超出20',
            'name.min'                => '角色名长度不得少于2',
            'name.eng_alpha'          => '角色名只能为英文字母组合',
            'name.unique'             => '系统已存在该角色名',
            'display_name.required'   => '角色展示名必须填写',
            'display_name.max'        => '角色展示名长度不要超出40',
            'display_name.min'        => '角色展示名长度不得少于3',
            'display_name.alpha_dash' => '角色展示名必须为常规字符',
            'description.max'         => '描述长度不要超出80',
        ];
    }

    public function beforeSaving($request)
    {
        $inputs = $request->only($this->fillable);
        return $inputs;
    }

    public function afterSaving($roleModel, $request)
    {
        if (array_key_exists('permissions', $request->all())) {
            $permissions = $request->input('permissions');  //这里提交的为数组
            if (is_array($permissions) && $permissions) {
                $roleModel->perms()->sync($permissions);  //同步角色权限
            }
        } else {
            $roleModel->perms()->sync([]);
        }
    }

}
