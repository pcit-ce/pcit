<?php

declare(strict_types=1);

use KhsCI\Support\Route;

try {
    Route::get('test', 'Test\TestController@test');

    Route::get('oauth', 'Users\LoginController@index');

    /* OAuth login*/

    Route::get('oauth/coding/login', 'Users\OAuthCodingController@getLoginUrl');

    Route::get('oauth/coding', 'Users\OAuthCodingController@getAccessToken');

    Route::get('oauth/github/login', 'Users\OAuthGitHubController@getLoginUrl');

    Route::get('oauth/github', 'Users\OAuthGitHubController@getAccessToken');

    Route::get('oauth/gitee/login', 'Users\OAuthGiteeController@getLoginUrl');

    Route::get('oauth/gitee', 'Users\OAuthGiteeController@getAccessToken');

    /*Webhooks*/

    Route::post('webhooks/github/add', 'Webhooks\gitHubController@add');

    Route::get('webhooks/github', 'Webhooks\GitHubController@receive');

    Route::post('webhooks/gitee/add', 'Webhooks\GiteeController@add');

    Route::get('webhooks/gitee', 'Webhooks\GiteeController@receive');

    Route::post('webhooks/coding/add', 'Webhooks\CondigController@add');

    Route::get('webhooks/coding', 'Webhooks\CodingController@receive');

    /*SEO*/

    Route::get('seo/baidu/xzh', '');

    /*Queue*/

    /*IM*/
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}

// 路由控制器填写错误

echo 'not found <hr>';

var_dump(Route::$obj);

var_dump(Route::$method);
