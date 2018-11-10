<?php

namespace Modules\Common\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 内容模型
 */
class Article extends Model
{
    /**
     * @var string 
     */
    protected $table = 'articles';

    /**
     * @var string 
     */
    protected $primaryKey = 'id';

    /**
     * @var array 
     */
    protected $fillable = [
        'title',
        'flag',
        'thumb',
        'slug',
        'cid',
        'description',
        'content',
    ];

    /**
     * getFlagAttribute
     * 
     * @param string $value
     * @return array
     */
    public function getFlagAttribute($value)
    {
        $value = rtrim($value, ',');
        if (is_string($value) && !empty($value)) {
            $flags = explode(',', $value);
        } else {
            $flags = [];
        }
        return $flags;
    }

    /**
     * category
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        // 模型名 外键 本键
        return $this->hasOne('Modules\Common\Models\Category', 'id', 'cid');
    }


    /**
     * labels
     * 
     * @return array
     */
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

    /**
     * rules
     * 
     * @param array $filters
     * @return array
     */
    public function rules($filters = [])
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
            'cid.*' => '分类id 非法',
        ];
    }

    /**
     * 保存前处理输入数据
     * 
     * @param array $inputs
     * @return array
     */
    public function beforeSaving($inputs)
    {
        if (empty($inputs['thumb'])) {
            $inputs['thumb'] = '';
        }
        if (is_array($inputs['flag']) && count($inputs['flag']) > 0) {
            $tmp_flag = implode($inputs['flag'], ',').',';
        } else {
            $tmp_flag = '';
        }
        $inputs['flag'] = $tmp_flag;
        return $inputs;
    }
}
