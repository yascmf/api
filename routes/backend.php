<?php

// 提供给 admin 对内业务管理系统 的接口
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['prefix' => 'user'], function ($api) {
        $api->get('me', function () {
            return [
                'hello' => 'world'
            ];
        });
    });
});