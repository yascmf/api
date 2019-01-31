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

    $api->post('upload/file', 'UploadController@postFile');

    $api->group(['middleware' => 'auth'],  function ($api) {
        $api->get('module-config', 'ModuleController@getModuleConfig');
        $api->get('me/profile', 'MeController@getProfile');
        $api->post('me/profile', ['middleware' => 'can:me-write', 'uses' => 'MeController@postProfile']);
        $api->post('me/logout', 'MeController@postLogout');
        $api->get('{module}', 'ModuleController@index');
        $api->post('{module}', 'ModuleController@store');
        $api->get('{module}/{id}', 'ModuleController@show');
        $api->put('{module}/{id}', 'ModuleController@update');

    });
});

