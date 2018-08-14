<?php

// 出于项目演示与测试目的而提供的对外公开型 `API` 接口

$router->group(['prefix' => 'api'], function () use ($router) {

    $router->get('/', function () {
        $json = [
            'code' => 404,
            'error' => '404 Not Found',
            'error_description' => 'please read documentation at `http://www.yascmf.com/docs/api.md`',
        ];
        return new \Illuminate\Http\JsonResponse($json, '404');
    });

    $router->get('ip', 'HomeController@getIp');
    $router->get('identity-card', 'HomeController@getIdentityCard');
});