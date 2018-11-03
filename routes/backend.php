<?php

// 提供给 admin 对内业务管理系统 的接口
$api = app('Dingo\Api\Routing\Router');
$api->group([
    'namespace' => 'Modules\Backend\Http\Controllers',
    'version' => 'v1',
    'middleware' => ['force-json'],
], function ($api) {
    
    $api->group(['prefix' => 'auth'], function ($api) {
        $api->post('login', 'AuthController@postLogin');
    });

    $api->group(['middleware' => 'auth'],  function ($api) {
        $api->get('user/info', 'UserController@getInfo');

        $api->resource('article', 'ArticleController');
    });
    
});
