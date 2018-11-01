<?php

// 出于项目演示与测试目的而提供的对外公开型 `API` 接口

$router->group(['prefix' => 'api', 'middleware' => 'force-json'], function () use ($router) {
    $router->get('ip', 'HomeController@getIp');
    $router->get('identity-card', 'HomeController@getIdentityCard');
});