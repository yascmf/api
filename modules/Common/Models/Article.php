<?php

namespace Modules\Common\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 内容模型
 */
class Article extends Model
{
    protected $table = 'articles';

    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'flag',
        'thumb',
        'slug',
        'cid',
        'description',
        'content',
    ];

    public function category()
    {
        //模型名 外键 本键
        return $this->hasOne('Modules\Common\Models\Category', 'id', 'cid');
    }

    public function labels()
    {
        return [
            'title' => '标题',
            'slug' => '标识符',
            'cid' => '分类id',
            'description' => '描述',
            'content' => '正文',
        ];
    }

    public function rules($filters)
    {
        $id = isset($filters['id']) && !empty($filters['id']) ? ','.$filters['id'].',id' : '';
        $rules = [
            'title'       => 'required|min:3|max:80',
            'slug'        => 'required|regex:/^[a-z0-9\-_]{1,120}$/|unique:articles,slug'.$id,
            'cid'         => 'required|exists:categories,id',
            'description' => 'required|min:10',
            'content'     => 'required|min:20',
        ];
        return $rules;
    }

    /**
     * 自定义验证信息
     *
     * @return array
     */
    public function messages()
    {
        return [
            'slug.regex' => ':attribute 非法',
        ];
    }

}
