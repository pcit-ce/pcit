<?php

use KhsCI\Support\Route;

try {
    Route::get('test', 'Test\TestController@test');

    Route::get('oauth/coding/login', 'Users\OAuthCodingController@getLoginUrl');

    Route::get('oauth/coding', 'Users\OAuthCodingController@getAccessToken');

    Route::post('webhooks/add', 'Webhooks\CodingController@add');

    Route::get('seo/baidu/xzh', '');

    Route::post('queue');

} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}

// 路由控制器填写错误

echo "not found <hr>";

var_dump(Route::$obj);

var_dump(Route::$method);

