<?php

return [
    'routers' => [
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
            'title' => '文章',
            'can' => '@article',
            'actions' => 'index,show,store,update',
            'model' => Modules\Common\Models\Article::class,
            'table' => 'articles',
            'index' => [
                // 'can' => ''
                'filters' => [
                    's_title' => ['title', 'like', '%{fieldValue}%'],
                    's_cid' => ['cid', '{fieldValue}'],
                ],
                'with' => 'category:id,name',
                'orderBy' => null,
            ],
            'store' => [
                'can' => 'article-write',
                'save' => [
                    'title' => 'e,trim',
                    'flag' => '',
                ],
            ],
        ]
    ]
];