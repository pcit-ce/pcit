<?php

declare(strict_types=1);

use KhsCI\Support\Response;
use KhsCI\Support\Route;

try {
    //Route::get('user/{id}', 'Test\TestController@test');
    Route::get('test', function () {
        return 1;
    });

    Route::get('test2', 'Test\TestController@test');

    Route::get('test3', 'Test\TestController');

    /*Test end*/

    Route::get('api', 'API\APIController');

    Route::get('status', 'StatusController');

    Route::get('about', 'AboutController');

    Route::get('team', 'TeamController');

    /* OAuth login*/

    Route::get('oauth', 'Users\LoginController@index');

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

    /*Profile*/

    Route::get('profile/coding/{user}', 'Profile\CodingController');

    Route::get('profile/gitee/{user}', 'Profile\GiteeController');

    Route::get('profile/github/{user}', 'Profile\GitHubController');

    /*Sync Repo*/

    Route::post('sync/coding', 'Sync\CodingController');
    Route::post('sync/gitee', 'Sync\GiteeController');
    Route::post('sync/github', 'Sync\GithubController');
} catch (Exception $e) {
    $code = $e->getCode();

    if (0 === $code) {
        $code = 500;
    }

    Response::json([
        'code' => $code,
        'message' => $e->getMessage() ?? 500,
        'api_url' => getenv('CI_HOST').'/api',
    ]);

    exit(1);
}

// 路由控制器填写错误

echo 'not found <hr>';

var_dump(Route::$obj);

var_dump(Route::$method);
