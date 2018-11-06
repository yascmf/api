<?php

// 带 * 为必填项，其它参考说明
return [
    'routers' => [  // * 路由模块组名
        'article',
        'category',
        'tag',
        'user',
        'role',
        'permission',
        'option',
        'log',
    ],
    'modules' => [
        'article' => [
            'title' => '文章',  // * 模块标题名
            'can' => '@article',  // 整个模块 controller 权限 - 没有请注释掉或者设置值为空字符串
            'actions' => 'index,show,store,update',  // * 支持的路由 actions
            'model' => Modules\Common\Models\Article::class,  // * 使用的模型类
            'table' => 'articles',  // 使用的表名 - 没有请注释掉
            'index' => [
                // 'can' => 'article-search',  // 此 action 路由权限 - 没有请注释掉或设置值为空字符串
                'filters' => [  // 搜索过滤串，目前只支持多 where 条件 - 没有设置值为空数组
                    's_title' => ['title', 'like', '%{fieldValue}%'],
                    's_cid' => ['cid', '{fieldValue}'],
                ],
                'with' => 'category:id,name',  // with关系 - 没有请注释掉或者设置值为 null
                'orderBy' => null,  // 排序 - 没有请设置值为 null
            ],
            'store' => [
                'can' => 'article-write',
                'save' => [
                    'title' => 'e,trim',
                    'flag' => '',
                ],
            ],
        ],
        'category' => [
            'title' => '分类',
            'can' => '@category',
            'actions' => 'index,show,store,update',
            'model' => Modules\Common\Models\Category::class,
            'table' => 'categories',
            'index' => [
                // 'can' => ''
                'filters' => [],
                'orderBy' => null,
            ],
            'store' => [
                'can' => 'category-write',
                'save' => [
                    'title' => 'e,trim',
                    'flag' => '',
                ],
            ],
        ]
    ]
];