<?php

declare(strict_types=1);

use KhsCI\Support\Route;

Route::get('test1/{id}', function ($id) {
    return $id;
});

Route::get('test3', 'Test\TestController');

Route::get('test4', 'Test\TestController@notExistsMethod');

Route::get('test5', 'Test\TestController@test5');

/*Test end*/

Route::get('api', 'API\APIController');

Route::get('status', 'StatusController');

Route::get('about', 'AboutController');

Route::get('team', 'TeamController');

Route::get('docs', 'DocsController');

/* OAuth login*/

Route::get('oauth', 'Users\LoginController@index');

Route::get('oauth/coding/login', 'Users\OAuthCodingController@getLoginUrl');

Route::get('oauth/coding', 'Users\OAuthCodingController@getAccessToken');

Route::get('oauth/github/login', 'Users\OAuthGitHubController@getLoginUrl');

Route::get('oauth/github', 'Users\OAuthGitHubController@getAccessToken');

Route::get('oauth/github_app/login', 'Users\OAuthGitHubAppController@getLoginUrl');

Route::get('oauth/github_app', 'Users\OAuthGitHubAppController@getAccessToken');

Route::get('oauth/gitee/login', 'Users\OAuthGiteeController@getLoginUrl');

Route::get('oauth/gitee', 'Users\OAuthGiteeController@getAccessToken');

Route::get('{git_type}/logout', 'Profile\LogOut');

/*Admin webhooks: list create delete*/

Route::post('webhooks/{git_type}/{username}/{repo_name}/{id}', 'Webhooks\Admin\Controller@add');

Route::get('webhooks/{git_type}/{username}/{repo_name}', 'Webhooks\Admin\Controller@list');

Route::delete('webhooks/{git_type}/{username}/{repo_name}/{id}', 'Webhooks\Admin\Controller@delete');

Route::post('webhooks/{git_type}/{username}/{repo_name}/{id}/activate', 'Webhooks\Admin\Controller@activate');

Route::delete('webhooks/{git_type}/{username}/{repo_name}/{id}/deactivate', 'Webhooks\Admin\Controller@deactivate');

/*Webhooks: receive git webhooks*/

Route::post('webhooks/github', 'Webhooks\GitHubController');

Route::post('webhooks/github_app', 'Webhooks\GitHubController@githubApp');

Route::post('webhooks/gitee', 'Webhooks\GiteeController');

Route::post('webhooks/coding', 'Webhooks\CodingController');

Route::post('webhooks/aliyun_docker_registry', 'Webhooks\AliyunDockerRegistryController');

// 获取所有接收到的 webhooks -> requests

/*SEO*/

Route::get('seo/baidu/xzh', '');

/*IM*/

/*Profile*/

Route::get('profile/coding/{username}', 'Profile\CodingController');

Route::get('profile/gitee/{username}', 'Profile\GiteeController');

Route::get('profile/github/{username}', 'Profile\GitHubController');

Route::get('profile/github_app/{username}', 'Profile\GitHubAppController');

// return information about an individual user.

Route::get('api/user/{git_type}/{user_id}', '');

// return information about the current user.

Route::get('api/user', '');

Route::get('api/user/{git_type}/{username}/beta_features', '');
Route::patch('api/user/{git_type}/{username}/beta_feature/{beta_feature_id}', '');
Route::delete('api/user/{git_type}/{username}/beta_feature/{beta_feature_id}', '');

/*Sync Repo*/

Route::post('api/user/{git_type}/{user_id}/sync', 'Profile\SyncController');

/*Status*/

Route::get('status/github/{username}/{repo_name}/{ref}', 'Status\GithubController@list');
Route::get('combined_status/github/{username}/{repo_name}/{commit_sha}', 'Status\GitHubController@listcombinedStatus');

/**Repos**/

Route::get('api/owner/{git_type}/{username}/repos', '');

Route::get('api/owner/{git_type}/{username}/active', '');

Route::get('api/orgs/{git_type}', '');

Route::get('api/orgs/{git_type}/{org_name}', '');

/* Builds */

Route::get('{git_type}/{username}/{repo_name}', 'Builds\ListController');
Route::get('api/repo/{git_type}/{username}/{repo_name}', 'Builds\ListController@post');

Route::get('{git_type}/{username}/{repo_name}/branches', 'Builds\ListController');
Route::get('api/repo/{git_type}/{username}/{repo_name}/branches', 'Builds\BranchesController@post');

Route::get('api/repo/{git_type}/{username}/{repo_name}/branch/{branch_name}', '');

Route::get('{git_type}/{username}/{repo_name}/builds', 'Builds\ListController');
Route::get('api/repo/{git_type}/{username}/{repo_name}/builds', 'Builds\ListController@list');

Route::get('{git_type}/{username}/{repo_name}/builds/{build_id}', 'Builds\ListController');
Route::get('api/builds', '');
Route::get('api/build/{build_id}', 'Builds\ListController@getBuildDetails');
Route::post('api/build/{build_id}/cancel', '');
Route::post('api/build/{build_id}/restart', '');

Route::get('api/repo/{git_type}/{username}/{repo_name}/env_vars', '');
Route::get('api/repo/{git_type}/{username}/{repo_name}/env_var/{env_var_id}', '');
Route::post('api/repo/{git_type}/{username}/{repo_name}/env_var/{env_var_id}', '');
Route::delete('api/repo/{git_type}/{username}/{repo_name}/env_var/{env_var_id}', '');

Route::get('{git_type}/{username}/{repo_name}/pull_requests', 'Builds\ListController');
Route::get('api/repo/{git_type}/{username}/{repo_name}/pull_requests', 'Builds\PullRequestsController@post');

Route::get('{git_type}/{username}/{repo_name}/settings', 'Builds\ListController');
Route::get('api/repo/{git_type}/{username}/{repo_name}/settings', 'Builds\SettingsController');
Route::get('api/repo/{git_type}/{username}/{repo_name}/setting/{setting_name}', 'Builds\SettingsController@get');
Route::patch('api/repo/{git_type}/{username}/{repo_name}/setting/{setting_name}', 'Builds\SettingsController@update');

Route::get('{git_type}/{username}/{repo_name}/requests', 'Builds\ListController');
Route::get('api/repo/{git_type}/{username}/{repo_name}/requests', 'Builds\RequestsController');
Route::post('api/repo/{git_type}/{username}/{repo_name}/requests', '');
Route::get('api/repo/{git_type}/{username}/{repo_name}/request/{requests_id}', '');

Route::get('{git_type}/{username}/{repo_name}/caches', 'Builds\ListController');
Route::get('api/repo/{git_type}/{username}/{repo_name}/caches', 'Builds\CachesController');
Route::delete('api/repo/{git_type}/{username}/{repo_name}/caches', 'Builds\CachesController@delete');

Route::get('api/repo/{git_type}/{username}/{repo_name}/crons', '');
Route::get('api/repo/{git_type}/{username}/{repo_name}/cron/{cron_id}', '');
Route::delete('api/repo/{git_type}/{username}/{repo_name}/cron/{cron_id}', '');
Route::get('api/repo/{git_type}/{username}/{repo_name}/branch/{branch_name}/cron', '');
Route::post('api/repo/{git_type}/{username}/{repo_name}/branch/{branch_name}/cron', '');

Route::get('{git_type}/{username}/{repo_name}/status', 'Builds\ShowStatusController');
Route::get('api/{git_type}/{username}/{repo_name}/status', 'Builds\ShowStatusController');
Route::get('{git_type}/{username}/{repo_name}/getstatus', 'Builds\ShowStatusController@getStatus');

Route::post('api/repo/{git_type}/{username}/{repo_name}/activate', '');
Route::post('api/repo/{git_type}/{username}/{repo_name}/deactivate', '');

Route::post('api/repo/{git_type}/{username}/{repo_name}/star', 'Builds\StarController');
Route::post('api/repo/{git_type}/{username}/{repo_name}/unstar', 'Builds\StarController@unstar');

/* Log */

Route::get('api/build/{build_id}/log', '');

Route::delete('api/build/{build_id}/log', '');

/* ICO */

Route::get('ico/canceled', 'Status\ShowStatusByICOController@canceled');
Route::get('ico/errored', 'Status\ShowStatusByICOController@errored');
Route::get('ico/failing', 'Status\ShowStatusByICOController@failing');
Route::get('ico/passing', 'Status\ShowStatusByICOController@passing');
Route::get('ico/pending', 'Status\ShowStatusByICOController@pending');
