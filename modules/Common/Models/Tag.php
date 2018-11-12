<?php

namespace Modules\Common\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 标签模型
 */
class Tag extends Model
{
    protected $table = 'tags';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
    ];

    public function labels()
    {
        return [
            'name' => '名称',
            'description' => '描述',
        ];
    }

    public function rules($filters = [])
    {
        return [
            'name' => 'required|alpha',
        ];
    }

    public function messages()
    {
        return [
            'name.alpha' => ':attribute 不能包含特殊字符',
        ];
    }
}
