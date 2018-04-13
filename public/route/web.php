<?php

use KhsCI\Support\Route;

try {
    Route::get('oauth/coding/login', 'Users\OAuthCodingController@getLoginUrl');

    Route::get('oauth/coding', 'Users\OAuthCodingController@getAccessToken');

    Route::get('seo/baidu/xzh', '');

    Route::post('queue');

} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}

// 路由控制器填写错误

var_dump(Route::$obj);

var_dump(Route::$method);

