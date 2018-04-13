<?php

use KhsCI\Support\Route;

try {
    Route::get('oauth/coding/login', 'Users\OAuthCodingController@getLoginUrl');
    Route::get('oauth/coding', 'Users\OAuthCodingController@getAccessToken');
} catch (Exception $e) {

}

// 路由控制器填写错误

var_dump(Route::$obj);

var_dump(Route::$method);

