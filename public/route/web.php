<?php

declare(strict_types=1);

use KhsCI\Support\Route;

Route::get('test', function () {
    return 1;
});

Route::get('test2', 'Test\TestController@test');

Route::get('test3', 'Test\TestController');

Route::get('test4', 'Test\TestController@notExistsMethod');

Route::get('test5', 'Test\TestController@test5');

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

/*Admin webhooks: list create delete*/

Route::post('webhooks/{git_type}/{username}/{repo}/{id}', 'Webhooks\Admin\Controller@add');

Route::get('webhooks/{git_type}/{username}/{repo}', 'Webhooks\Admin\Controller@list');

Route::delete('webhooks/{git_type}/{username}/{repo}/{id}', 'Webhooks\Admin\Controller@delete');

Route::post('webhooks/{git_type}/{username}/{repo}/{id}/activate', 'Webhooks\Admin\Controller@activate');

Route::delete('webhooks/{git_type}/{username}/{repo}/{id}/deactivate', 'Webhooks\Admin\Controller@deactivate');

/*Webhooks: receive git webhooks*/

Route::post('webhooks/github', 'Webhooks\GitHubController');

Route::post('webhooks/gitee', 'Webhooks\GiteeController');

Route::post('webhooks/coding', 'Webhooks\CodingController');

// 获取所有接收到的 webhooks -> requests

/*SEO*/

Route::get('seo/baidu/xzh', '');

/*Queue*/

Route::post('queue', 'Queue\QueueController');

Route::get('queue', 'Queue\QueueController');

/*IM*/

/*Profile*/

Route::get('profile/coding/{username}', 'Profile\CodingController');

Route::get('profile/gitee/{username}', 'Profile\GiteeController');

Route::get('profile/github/{username}', 'Profile\GitHubController');

/*Sync Repo*/

Route::post('sync/coding', 'Sync\CodingController');
Route::post('sync/gitee', 'Sync\GiteeController');
Route::post('sync/github', 'Sync\GithubController');

/*Status*/

Route::get('status/github/{username}/{repo}/{ref}', 'Status\GithubController@list');
Route::get('combined_status/github/{username}/{repo}/{commit_sha}', 'Status\GitHubController@listcombinedStatus');

/* Builds */

Route::get('{git_type}/{username}/{repo}', 'Builds\ListController');
Route::post('{git_type}/{username}/{repo}', 'Builds\ListController@post');

Route::get('{git_type}/{username}/{repo}/branches', 'Builds\ListController');
Route::get('{git_type}/{username}/{repo}/{branch}/status', 'Builds\ShowStatusController');
Route::post('{git_type}/{username}/{repo}/branches', 'Builds\BranchesController@post');

Route::get('{git_type}/{username}/{repo}/builds', 'Builds\ListController');
Route::post('{git_type}/{username}/{repo}/builds', 'Builds\ListController@list');

Route::get('{git_type}/{username}/{repo}/builds/{id}', 'Builds\ListController');

Route::get('{git_type}/{username}/{repo}/pull_requests', 'Builds\ListController');

Route::get('{git_type}/{username}/{repo}/pull_requests/{id}', 'Builds\ListController');

Route::get('{git_type}/{username}/{repo}/settings', 'Builds\ListController');

Route::get('{git_type}/{username}/{repo}/requests', 'Builds\ListController');
Route::post('{git_type}/{username}/{repo}/requests', 'Builds\ListController@list');

Route::get('{git_type}/{username}/{repo}/caches', 'Builds\ListController');

/* Star */

Route::post('{git_type}/{username}/{repo}/star', 'Builds\ListController');
Route::delete('{git_type}/{username}/{repo}/star', 'Builds\ListController');

/* ICO */

Route::get('ico/canceled', 'Status\ShowStatusController@canceled');
Route::get('ico/errored', 'Status\ShowStatusController@errored');
Route::get('ico/failing', 'Status\ShowStatusController@failing');
Route::get('ico/passing', 'Status\ShowStatusController@passing');
Route::get('ico/pending', 'Status\ShowStatusController@pending');
