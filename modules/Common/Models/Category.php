<?php

namespace Modules\Common\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 分类模型
 */
class Category extends Model
{
    protected $table = 'categories';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'sort',
        'slug',
    ];
    
    public function labels()
    {
        return [
            'name' => '名称',
            'sort' => '排序',
            'slug' => '标识符',
        ];
    }
    
    public function rules($filters)
    {
        $id = isset($filters['id']) && !empty($filters['id']) ? ','.$filters['id'].',id' : '';
        $rules = [
            'name' => 'required|alpha',
            'slug' => 'required|regex:/^[a-z0-9\-_]{3,20}$/|unique:categories,slug'.$id,
            'sort' => 'required|numeric',
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            'name.alpha' => ':attribute 不能包含特殊字符',
            'slug.regex' => ':attribute 不符合组合规则([a-z0-9\-_]{3,20})',
        ];
    }

}
