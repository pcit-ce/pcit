<?php

declare(strict_types=1);

use KhsCI\Support\Env;
use KhsCI\Support\Route;

Route::get('test1/{id}', function ($id) {
    return $id;
});

Route::get('test3', 'Test\TestController');

Route::get('test4', 'Test\TestController@notExistsMethod');

Route::get('test5', 'Test\TestController@test5');

Route::post('test5', 'Test\TestController@test5');

/*Test end*/

Route::get('api', 'APIController');

Route::get('sitemap', 'SiteMapController');

Route::get('status', 'StatusController');

Route::get('about', 'AboutController');

Route::get('team', 'TeamController');

Route::get('docs', 'DocsController');

Route::get('wechat', 'WeChatController');

Route::get('blog', 'BlogController');

/* OAuth login*/

Route::get('oauth', 'Users\LoginController@index');

Route::get('oauth/coding/login', 'Users\OAuthCodingController@getLoginUrl');

Route::get('oauth/coding', 'Users\OAuthCodingController@getAccessToken');

Route::get('oauth/github/login', 'Users\OAuthGitHubController@getLoginUrl');

Route::get('oauth/github', 'Users\OAuthGitHubController@getAccessToken');

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

Route::post('webhooks/gogs', 'Webhooks\GogsController');

Route::post('webhooks/gitee', 'Webhooks\GiteeController');

Route::post('webhooks/coding', 'Webhooks\CodingController');

Route::post('webhooks/'.Env::get('CI_ALIYUN_REGISTRY_WEBHOOKS_ADDRESS', 'aliyun_docker_registry'),
    'Webhooks\AliyunDockerRegistryController');

// 获取所有接收到的 webhooks -> requests

/*SEO*/

Route::get('seo/baidu/xzh', '');

/*IM*/

/*Profile*/

Route::get('profile/coding/{username}', 'Profile\CodingController');

Route::get('profile/gitee/{username}', 'Profile\GiteeController');

Route::get('profile/github/{username}', 'Profile\GitHubController');

// return information about an individual user.

Route::get('api/user/{git_type}/{username}', 'Users\UserInfoController@find');

// return information about the current user.

Route::get('api/user', 'Users\UserInfoController');

Route::get('api/user/beta_features', 'Users\BetaFeatureController');
Route::patch('api/user/beta_feature/{beta_feature_id}', 'Users\BetaFeatureController@enable');
Route::delete('api/user/beta_feature/{beta_feature_id}', 'Users\BetaFeatureController@delete');

/*Sync User info*/

Route::post('api/user/sync', 'Profile\SyncController');

/*Status*/

Route::get('status/github/{username}/{repo_name}/{ref}', 'Status\GithubController@list');
Route::get('combined_status/github/{username}/{repo_name}/{commit_sha}',
    'Status\GitHubController@listcombinedStatus');

/**Repos**/

Route::get('{git_type}/{username}', 'Users\RepositoriesController@index');

Route::get('api/repos', 'Users\RepositoriesController');

Route::get('api/repos/{git_type}/{username}', 'Users\RepositoriesController@list'); //某用户名下的仓库列表

Route::get('api/repo/{git_type}/{username}/{repo_name}', 'Users\RepositoriesController@find'); // 列出某仓库详情

Route::get('api/user/{git_type}/{username}/active', 'Builds\ActiveController');

/* orgs */

Route::get('api/orgs', 'Users\OrganizationsController');

Route::get('api/org/{git_type}/{org_name}', 'Users\OrganizationsController@find');

/* Builds */

Route::get('{git_type}/{username}/{repo_name}', 'Builds\IndexController');
Route::get('api/repo/{git_type}/{username}/{repo_name}/build/current', 'Builds\BuildsController@repoCurrent');

Route::get('{git_type}/{username}/{repo_name}/branches', 'Builds\IndexController');
Route::get('api/repo/{git_type}/{username}/{repo_name}/branches', 'Builds\BranchesController');
Route::get('api/repo/{git_type}/{username}/{repo_name}/branch/{branch_name}', 'Builds\BranchesController@find');

Route::get('{git_type}/{username}/{repo_name}/builds', 'Builds\IndexController');
Route::get('api/repo/{git_type}/{username}/{repo_name}/builds', 'Builds\BuildsController@listByRepo');

Route::get('{git_type}/{username}/{repo_name}/builds/{build_id}', 'Builds\IndexController');
Route::get('api/builds', 'Builds\BuildsController');
Route::get('api/build/{build_id}', 'Builds\BuildsController@find');
Route::post('api/build/{build_id}/cancel', 'Builds\BuildsController@cancel');
Route::post('api/build/{build_id}/restart', 'Builds\BuildsController@restart');

Route::get('api/repo/{username}/{repo_name}/env_vars', 'Builds\EnvController');
Route::post('api/repo/{username}/{repo_name}/env_vars', 'Builds\EnvController@create');
Route::get('api/repo/{username}/{repo_name}/env_var/{env_var_id}', 'Builds\EnvController@find');
Route::patch('api/repo/{username}/{repo_name}/env_var/{env_var_id}', 'Builds\EnvController@update');
Route::delete('api/repo/{username}/{repo_name}/env_var/{env_var_id}', 'Builds\EnvController@delete');

Route::get('{git_type}/{username}/{repo_name}/pull_requests', 'Builds\IndexController');
Route::get('api/repo/{git_type}/{username}/{repo_name}/pull_requests', 'Builds\PullRequestsController@post');

Route::get('{git_type}/{username}/{repo_name}/settings', 'Builds\IndexController');
Route::get('api/repo/{username}/{repo_name}/settings', 'Builds\SettingsController');
Route::get('api/repo/{username}/{repo_name}/setting/{setting_name}', 'Builds\SettingsController@get');
Route::patch('api/repo/{username}/{repo_name}/setting/{setting_name}', 'Builds\SettingsController@update');

Route::get('{git_type}/{username}/{repo_name}/requests', 'Builds\IndexController');
Route::get('api/repo/{username}/{repo_name}/requests', 'Builds\RequestsController');
Route::post('api/repo/{username}/{repo_name}/requests', 'Builds\RequestsController@create');
Route::get('api/repo/{username}/{repo_name}/request/{requests_id}', 'Builds\RequestsController@find');

Route::get('{git_type}/{username}/{repo_name}/caches', 'Builds\IndexController');
Route::get('api/repo/{username}/{repo_name}/caches', 'Builds\CachesController');
Route::delete('api/repo/{username}/{repo_name}/caches', 'Builds\CachesController@delete');

Route::get('api/repo/{username}/{repo_name}/crons', 'Builds\CronController');
Route::get('api/repo/{username}/{repo_name}/cron/{cron_id}', 'Builds\CronController@find');
Route::delete('api/repo/{username}/{repo_name}/cron/{cron_id}', 'Builds\CronController@delete');
Route::get('api/repo/{username}/{repo_name}/branch/{branch_name}/cron', 'Builds\CronController@findByBranch');
Route::post('api/repo/{username}/{repo_name}/branch/{branch_name}/cron', 'Builds\CronController@createByBranch');

Route::get('{git_type}/{username}/{repo_name}/status', 'Builds\ShowStatusController');
Route::get('{git_type}/{username}/{repo_name}/getstatus', 'Builds\ShowStatusController@getStatus');
Route::get('api/repo/{git_type}/{username}/{repo_name}/status', 'Builds\ShowStatusController');

Route::post('api/repo/{username}/{repo_name}/activate', 'Builds\ActiveController@activate');
Route::post('api/repo/{username}/{repo_name}/deactivate', 'Builds\ActiveController@deactivate');

Route::post('api/repo/{username}/{repo_name}/star', 'Builds\StarController');
Route::post('api/repo/{username}/{repo_name}/unstar', 'Builds\StarController@unstar');

/* Log */

Route::get('api/build/{build_id}/log', 'Builds\LogController');

Route::delete('api/build/{build_id}/log', 'Builds\LogController@delete');

/* ICO */

Route::get('ico/canceled', 'Status\ShowStatusByICOController@canceled');
Route::get('ico/errored', 'Status\ShowStatusByICOController@errored');
Route::get('ico/failed', 'Status\ShowStatusByICOController@failed');
Route::get('ico/in_progress', 'Status\ShowStatusByICOController@in_progress');
Route::get('ico/missconfig', 'Status\ShowStatusByICOController@missconfig');
Route::get('ico/passed', 'Status\ShowStatusByICOController@passed');
Route::get('ico/pending', 'Status\ShowStatusByICOController@pending');
Route::get('ico/unknown', 'Status\ShowStatusByICOController@unknown');
/* API Token */

Route::post('api/user/token', 'APITokenController@find');

Route::get('wechat', 'WeChat\MessageServer');
Route::post('wechat', 'WeChat\MessageServer');

/* System */

Route::get('api/ci/oauth_client_id', 'System\OAuthClientController');
