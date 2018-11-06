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
            'can' => 'any:@article,store|update:article-write',
            'action' => 'index,show,store,update',
            'model' => Modules\Common\Models\Article::class,
            'table' => 'articles',
        ]
    ]
];