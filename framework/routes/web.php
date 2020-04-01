<?php

declare(strict_types=1);

// test
Route::get('test1/{id}', function ($id) {
    return $id;
});

Route::get('test3', 'Test\TestController@test3');

Route::get('test4', 'Test\TestController@notExistsMethod');

Route::get('test5', 'Test\TestController@test5');

if (\App::environment('testing')) {
    Route::get('testing', function () {
        return '1';
    });

    Route::get('testing/{id}', function ($id) {
        return $id;
    });
}

// test end

/* Dashboard */
Route::get('{git_type}/dashboard', function () {
    return 'Coming Soon';
});

/* Admin */
Route::get('admin', function () {
    return 'Coming Soon';
});

Route::get('api', 'IndexController@api');
Route::get('sitemap', 'IndexController@sitemap');
Route::get('status', 'IndexController@status');
Route::get('about', 'IndexController@about');
Route::get('team', 'IndexController@team');
Route::get('docs', 'IndexController@docs');
Route::get('wechat', 'IndexController@wechat');
Route::get('blog', 'IndexController@blog');
Route::get('issues', 'IndexController@issues');
Route::get('support', 'IndexController@support');
Route::get('changelog', 'IndexController@changelog');
Route::get('ce', 'IndexController@ce');
Route::get('ee', 'IndexController@ee');
Route::get('why', 'IndexController@why');
Route::get('donate', 'IndexController@donate');
Route::get('plugins', 'IndexController@plugins');
Route::get('terms-of-service', 'IndexController@terms_of_service');
Route::get('privacy-policy', 'IndexController@privacy_policy');

/* OAuth login*/
Route::get('oauth', 'Users\LoginController@index');

Route::get('oauth/${git_type}/login', 'OAuth\IndexController@getLoginUrl');

Route::get('oauth/${git_type}', 'OAuth\IndexController@getAccessToken');

Route::get('{git_type}/logout', 'Profile\LogOut');

/*Admin webhooks: list create delete*/
Route::post('webhooks/{git_type}/{username}/{repo_name}/{id}', 'Webhooks\Controller@add');

Route::get('webhooks/{git_type}/{username}/{repo_name}', 'Webhooks\Controller@list');

Route::delete('webhooks/{git_type}/{username}/{repo_name}/{id}', 'Webhooks\Controller@delete');

Route::post('webhooks/{git_type}/{username}/{repo_name}/{id}/activate', 'Webhooks\Controller@activate');

Route::delete('webhooks/{git_type}/{username}/{repo_name}/{id}/deactivate', 'Webhooks\Controller@deactivate');

/*Webhooks server: receive git webhooks*/
Route::post('webhooks/${git_type}', 'Webhooks\Server\IndexController');

/*Profile*/
Route::get('profile/${git_type}/{username}', 'Profile\IndexController');

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
// repo list
Route::get('{git_type}/{username}', 'Builds\IndexController');

Route::get('api/repos', 'Users\RepositoriesController');

Route::get('api/repos/{git_type}/{username}', 'Users\RepositoriesController@list'); //某用户名下的仓库列表

Route::get('api/repo/{git_type}/{username}/{repo_name}', 'Users\RepositoriesController@find'); // 列出某仓库详情

Route::get('api/user/{git_type}/{username}/active', 'Builds\ActiveController');

/* orgs */
Route::get('api/orgs', 'Users\OrganizationsController');

Route::get('api/org/{git_type}/{org_name}', 'Users\OrganizationsController@find');

/* Builds */
// repo
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

// job
Route::get('{git_type}/{username}/{repo_name}/jobs/{job_id}', 'Builds\IndexController');
// job artifact
Route::get('api/{git_type}/{username}/{repo_name}/artifacts', 'Builds\Artifact@listByRepo');
Route::get('api/{git_type}/{username}/{repo_name}/jobs/{job_id}/artifacts', 'Builds\Artifact@listByJob');

Route::get('api/{git_type}/{username}/{repo_name}/jobs/{job_id}/artifacts/{file_name}', 'Builds\Artifact');
Route::get('api/{git_type}/{username}/{repo_name}/jobs/{job_id}/artifacts/{file_name}/{format}', 'Builds\Artifact@download');
Route::delete('api/{git_type}/{username}/{repo_name}/jobs/{job_id}/artifacts/{file_name}', 'Builds\Artifact@delete');

Route::get('api/jobs', 'Builds\JobController@list');
Route::get('api/job/{job_id}', 'Builds\JobController@find');
Route::post('api/job/{job_id}/cancel', 'Builds\JobController@cancel');
Route::post('api/job/{job_id}/restart', 'Builds\JobController@restart');

Route::get('{git_type}/{username}/{repo_name}/pull_requests', 'Builds\IndexController');
Route::get('api/repo/{git_type}/{username}/{repo_name}/pull_requests', 'Builds\PullRequestsController@post');

Route::get('api/repo/{username}/{repo_name}/env_vars', 'Builds\EnvController');
Route::post('api/repo/{username}/{repo_name}/env_vars', 'Builds\EnvController@create');
Route::get('api/repo/{username}/{repo_name}/env_var/{env_var_id}', 'Builds\EnvController@find');
Route::patch('api/repo/{username}/{repo_name}/env_var/{env_var_id}', 'Builds\EnvController@update');
Route::delete('api/repo/{username}/{repo_name}/env_var/{env_var_id}', 'Builds\EnvController@delete');

Route::get('{git_type}/{username}/{repo_name}/settings', 'Builds\IndexController');
Route::get('api/repo/{username}/{repo_name}/settings', 'Builds\SettingsController');
Route::get('api/repo/{username}/{repo_name}/setting/{setting_name}', 'Builds\SettingsController@get');
Route::patch('api/repo/{username}/{repo_name}/setting/{setting_name}', 'Builds\SettingsController@update');

Route::get('{git_type}/{username}/{repo_name}/requests', 'Builds\IndexController');
Route::get('api/repo/{git_type}/{username}/{repo_name}/requests', 'Builds\RequestsController');
Route::post('api/repo/{username}/{repo_name}/requests', 'Builds\RequestsController@create');
Route::get('api/repo/{username}/{repo_name}/request/{requests_id}', 'Builds\RequestsController@find');

Route::get('{git_type}/{username}/{repo_name}/caches', 'Builds\IndexController');
Route::get('api/repo/{username}/{repo_name}/caches', 'Builds\CachesController');
Route::delete('api/repo/{username}/{repo_name}/caches', 'Builds\CachesController@delete');
Route::delete('api/repo/{username}/{repo_name}/caches/{branch}', 'Builds\CachesController@delete');

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

Route::post('api/repo/{username}/{repo_name}/star', 'Repos\StarController');
Route::post('api/repo/{username}/{repo_name}/unstar', 'Repos\StarController@unstar');

/* Log */
Route::get('api/job/{job_id}/log', 'Builds\LogController');
Route::delete('api/job/{job_id}/log', 'Builds\LogController@delete');

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
Route::post('api/user/token', 'Users\JWTController@generate');

// wechat
Route::match(['get', 'post'], 'wechat', 'WeChat\MessageServer');

/* System */
Route::get('api/ci/oauth_client_id', 'System\SystemController@getOAuthClientId');
Route::get('api/ci/github_app_installation/{uid}', 'System\SystemController@getGitHubAppInstallationUrl');
Route::get('api/ci/github_app_settings/{org_name}', 'System\SystemController@getGitHubAppSettingsUrl');
Route::get('api/ci/about', 'System\SystemController@about');
Route::get('api/ci/changelog', 'System\SystemController@changelog');
Route::get('api/ci/github_trending/{language}/{since}', 'System\SystemController@gitHubTrending');

Route::get('api/metrics', 'System\Metrics');

Route::get('api/healthz', 'System\Healthz');
Route::get('api/healthz/cache', 'System\Healthz@cache');
Route::get('api/healthz/database', 'System\Healthz@database');

Route::get('api/readyz', 'System\Healthz');
Route::get('api/readyz/cache', 'System\Healthz@cache');
Route::get('api/readyz/database', 'System\Healthz@database');

Route::get('api/livez', 'System\Healthz');
Route::get('api/livez/cache', 'System\Healthz@cache');
Route::get('api/livez/database', 'System\Healthz@database');

Route::get('api/openapi', 'System\OpenAPI');
Route::get('api/openapi/v3', 'System\OpenAPI');

Route::get('api/config_schema.json', function () {
    return Response::json(file_get_contents(base_path().'config/config_schema.json'), true);
});

Route::get('validate', function () {
    return 'Coming Soon';
});

Route::post('validate', 'Config\Validate');
Route::post('api/validate', 'Config\Validate');

/* Issues */
Route::patch('api/repo/${username}/${repo_name}/issues/translate/${issue_number}',
    'Repos\Issues@translate');

/* Demo */
Route::get('websocket/server', 'Demo\WebSocket\WebSocketController');
Route::get('sse/server', 'Demo\SSE\SSEController');
Route::get('websocket/client', 'Demo\WebSocket\WebSocketController@client');
Route::get('sse/client', 'Demo\SSE\SSEController@client');
