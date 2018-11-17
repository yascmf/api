<?php

/**
 * 第三方服务配置
 */
return [
    /*---- API 相关配置 START -----*/
    'api' => [
        'login_token' => [
            'prefix' => 'api_token:',
            'ttl' => 7200,  // 2 hours
            'refresh_ttl' => 600,  // before expired time 10 minutes
        ]
    ],
];
