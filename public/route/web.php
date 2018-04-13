<?php

declare(strict_types=1);
use KhsCI\Support\Route;

try {
    Route::get('test', 'Test\TestController@test');

    Route::get('oauth', 'Users\LoginController@index');

    Route::get('oauth/coding/login', 'Users\OAuthCodingController@getLoginUrl');

    Route::get('oauth/coding', 'Users\OAuthCodingController@getAccessToken');

    Route::get('oauth/github/login', 'Users\OAuthGitHubController@getLoginUrl');

    Route::get('oauth/github', 'Users\OAuthGitHubController@getAccessToken');

    Route::post('webhooks/add', 'Webhooks\CodingController@add');

    Route::get('seo/baidu/xzh', '');

    Route::post('queue');
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}

// 路由控制器填写错误

echo 'not found <hr>';

var_dump(Route::$obj);

var_dump(Route::$method);
