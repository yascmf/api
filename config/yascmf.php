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
        'topic',
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
                'orderBy' => null,  // 排序，已经有默认的 created_at desc 排序，无须再传入此排序 - 没有请设置值为 null
            ],
            'store' => [
                'can' => 'article-write',
            ],
            'show' => [
                
            ],
            'update' => [
                'can' => 'article-write',
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
            ],
        ],
        'tag' => [
            'title' => '分类',
            'can' => '@tag',
            'actions' => 'index,show,store,update',
            'model' => Modules\Common\Models\Tag::class,
            'table' => 'categories',
            'index' => [
                // 'can' => ''
                'filters' => [],
                'orderBy' => null,
            ],
            'store' => [
                'can' => 'tag-write',
            ],
        ],
        'user' => [
            'title' => '管理用户',
            'can' => '@user',
            'actions' => 'index,show,store,update',
            'model' => App\User::class,
            'table' => 'users',
            'index' => [
                // 'can' => ''
                'filters' => [],
                'orderBy' => null,
                'with' => 'roles:id,name',  // with关系 - 没有请注释掉或者设置值为 null
            ],
            'store' => [
                'can' => 'user-write',
            ],
        ],
        'role' => [
            'title' => '角色',
            'can' => '@role',
            'actions' => 'index,show,store,update',
            'model' => App\Role::class,
            'table' => 'roles',
            'index' => [
                // 'can' => ''
                'filters' => [],
                'orderBy' => null,
            ],
            'store' => [
                'can' => 'role-write',
            ],
        ],
        'permission' => [
            'title' => '权限',
            'can' => '@permission',
            'actions' => 'index',
            'model' => App\Permission::class,
            'table' => 'permissions',
            'index' => [
                // 'can' => ''
                'filters' => [],
                'orderBy' => null,
            ]
        ],
    ]
];