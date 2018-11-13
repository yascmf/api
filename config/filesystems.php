<?php

return [
    'disks' => [
        //...
        'qiniu' => [
            'driver'     => 'qiniu',
            'access_key' => env('QINIU_ACCESS_KEY'),
            'secret_key' => env('QINIU_SECRET_KEY'),
            'bucket'     => env('QINIU_BUCKET'),
            'domain'     => env('QINIU_DOMAIN'), // or host: https://xxxx.clouddn.com
        ],
        //...
    ]
];