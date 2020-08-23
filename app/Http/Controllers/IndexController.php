<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use PCIT\Framework\Attributes\Route;

class IndexController
{
    @@Route('get', 'about')
    public function about(): array
    {
        return [
            'code' => 200,
            'about' => 'The goal of PCIT is to build CI/CD System by PHP Powered by Docker and Kubernetes',
        ];
    }

    @@Route('get', 'blog')
    public function blog(): void
    {
        \Response::redirect('https://khs1994.github.io/categories/CI/PCIT/');
    }

    @@Route('get', 'api')
    public function api()
    {
        $ci_host = config('app.host').'/api';

        $array = [
            'user' => [
                'current_user_url' => $ci_host.'/user',
                'find' => $ci_host.'/user/{git_type}/{username}',
                'sync' => $ci_host.'/user/sync',
                'beta_feature' => [
                    'get' => $ci_host.'/user/beta_features',
                    'update@patch' => $ci_host.'/user/beta_feature/{beta_feature.id}',
                    'delete' => $ci_host.'/user/beta_feature/{beta_feature.id}',
                ],
                'active' => $ci_host.'/user/{git_type}/{username}/active', // 返回某用户（或组织）处于活跃状态的仓库列表
            ],
            'repo' => [
                'current_user_repositories_url' => $ci_host.'/repos',
                'user\'s repo' => $ci_host.'/repos/{git_type}/{username}',
                'branch' => [
                    'list@get' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/branches',
                    'find@get' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/branch/{branch.name}',
                ],
                'env_vars' => [
                    'list' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/env_vars',
                    'create@post' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/env_vars',
                    'find' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/env_var/{env_var.id}',
                    'update@patch' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/env_var/{env_var.id}',
                    'delete' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/env_var/{env_var.id}',
                ],
                'settings' => [
                    'list' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/settings',
                    'get' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/setting/{setting.name}',
                    'update@patch' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/setting/{setting.name}',
                ],
                'requests' => [
                    'list' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/requests',
                    'create@post' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/requests',
                    'get' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/request/{requests.id}',
                ],
                'caches' => [
                    'list' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/caches',
                    'delete' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/caches',
                ],
                'crons' => [
                    'list' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/crons',
                    'find' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/cron/{cron.id}',
                    'delete' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/cron/{cron.id}',
                    'findByBranch' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/branch/{branch.name}/cron',
                    'createByBranch@post' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/branch/{branch.name}/cron',
                ],
                'status' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/status',
                'activate@post' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/activate',
                'deactivate@post' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/deactivate',
                'start@post' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/star',
                'unstar@post' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/unstar',
            ],
            'builds' => [
                'current_user_builds_url' => $ci_host.'/builds',
                'listByRepo' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/builds',
                'findByRepoCurrent' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/build/current',
                'find' => $ci_host.'/build/{build_id}',
                'cancel@post' => $ci_host.'/build/{build.id}/cancel',
                'restart@post' => $ci_host.'/build/{build.id}/restart',
                'log' => [
                    'find' => $ci_host.'/build/{build.id}/log',
                    'delete' => $ci_host.'/build/{build.id}/log',
                ],
            ],
            'orgs' => [
                'user_organizations_url' => $ci_host.'/orgs', // Returns a list of organizations the current user is a member of.
                'find' => $ci_host.'/org/{git_type}/{org.name}',
            ],
            'system' => [
                'oauth_client_id' => $ci_host.'/ci/oauth_client_id',
                'metrics' => $ci_host.'/metrics',
                'healthz' => $ci_host.'/healthz',
                'readyz' => $ci_host.'/readyz',
                'livez' => $ci_host.'/livez',
                'openapi' => $ci_host.'/openapi',
                'openapi_v3' => $ci_host.'/openapi/v3',
                'validate@post' => $ci_host.'/validate',
            ],
            'github' => [
                'app' => [
                    'detail' => $ci_host.'/github/app',
                    'new' => $ci_host.'/github/app/new',
                    'callback' => $ci_host.'/github/app/new/callback',
                ],
            ],
        ];

        ksort($array);

        $array['sitemap'] = config('app.host').'/sitemap';

        return $array;
    }

    @@Route('get', 'docs')
    public function docs(): void
    {
        \Response::redirect('https://github.com/pcit-ce/pcit/tree/master/docs');
    }

    @@Route('get', 'plugins')
    public function plugins(): void
    {
        \Response::redirect('https://docs.ci.khs1994.com/plugins/');
    }

    @@Route('get', 'sitemap')
    public function sitemap()
    {
        $host = config('app.host');

        return [
            'homepage' => $host,
            'login' => $host.'/login',
            'beta' => $host.'/beta',
            'dashboard' => $host.'/{git_type}/dashboard',
            'api' => $host.'/api',
            'about' => $host.'/about',
            'team' => $host.'/team',
            'blog' => $host.'/blog',
            'docs' => $host.'/docs',
            'changelog' => $host.'/changelog',
            'wechat' => $host.'/wechat',
            'status' => $host.'/status',
            'feedback' => 'https://github.com/pcit-ce/pcit/issues',
            'validate@get' => $host.'/validate',
            'validate@post' => $host.'/validate',
            'tests' => [
                'route not found' => $host.'/not_exists_url',
                'test' => $host.'/test5',
                'testSession' => $host.'/test3',
            ],
            'oauth' => [
                'coding' => $host.'/oauth/coding/login',
                'gitee' => $host.'/oauth/gitee/login',
                'github' => $host.'/oauth/github/login',
            ],
            'logout' => [
                'coding' => $host.'/coding/logout',
                'gitee' => $host.'/gitee/logout',
                'github' => $host.'/github/logout',
            ],
            'profile' => [
                'coding' => $host.'/profile/coding/{username}',
                'gitee' => $host.'/profile/gitee/{username}',
                'github' => $host.'/profile/github/{username}',
            ],
            'webhoks@admin' => [
                'list@get' => [
                    'coding' => $host.'/webhooks/coding/{username}/{repo}',
                    'gitee' => $host.'/webhooks/gitee/{username}/{repo}',
                ],
                'cteate@post' => [
                    'coding' => $host.'/webhooks/coding/{username}/{repo}/{id}',
                    'gitee' => $host.'/webhooks/gitee/{username}/{repo}/{id}',
                ],
                'delete@delete' => [
                    'coding' => $host.'/webhooks/coding/{username}/{repo}/{id}',
                    'gitee' => $host.'/webhooks/gitee/{username}/{repo}/{id}',
                ],
                'activate@post' => [
                    'coding' => $host.'/webhooks/coding/{username}/{repo}/{id}/activate',
                    'gitee' => $host.'/webhooks/gitee/{username}/{repo}/{id}/activate',
                ],
                'deactivate@post' => [
                    'coding' => $host.'/webhooks/coding/{username}/{repo}/{id}/deactivate',
                    'gitee' => $host.'/webhooks/gitee/{username}/{repo}/{id}/deactivate',
                ],
            ],
            'webhooks@receive' => [
                'coding' => $host.'/webhooks/coding',
                'gitee' => $host.'/webhooks/gitee',
                'github' => $host.'/webhooks/github',
                'custom_provider' => $host.'/webhooks/custom_provider',
            ],
            'repo' => [
                'main' => $host.'/{git_type}/{username}/{repo}',
                'branches' => [
                    'main' => $host.'/{git_type}/{username}/{repo}/branches',
                    'status' => $host.'/{git_type}/{username}/{repo}/{branch}/status',
                ],
                'builds' => [
                    'main' => $host.'/{git_type}/{username}/{repo}/builds',
                    'id' => $host.'/{git_type}/{username}/{repo}/builds/{id}',
                ],
                'pull_requests' => [
                    'main' => $host.'/{git_type}/{username}/{repo}/pull_requests',
                    'id' => $host.'/{git_type}/{username}/{repo}/pull_requests/{id}',
                ],
                'settings' => $host.'/{git_type}/{username}/{repo}/settings',
                'requests' => $host.'/{git_type}/{username}/{repo}/requests',
                'caches' => $host.'/{git_type}/{username}/{repo}/caches',
                'star@post' => $host.'/{git_type}/{username}/{repo}/star',
                'unstar@delete' => $host.'/{git_type}/{username}/{repo}/unstar',
                'status' => $host.'/{git_type}/{username}/{repo}/status',
                'getstatus' => $host.'/{git_type}/{username}/{repo}/getstatus',
            ],
            'statuses@github' => [
                'list@get' => $host.'/status/github/{username}/{repo}/{ref}',
                'combinedStatus@get' => $host.'/combined_status/github/{username}/{repo}/{ref}',
            ],
            'ico' => [
                'canceled' => $host.'/ico/canceled',
                'errored' => $host.'/ico/errored',
                'failing' => $host.'/ico/failed',
                'in progress' => $host.'/ico/in_progress',
                'missconfig' => $host.'/ico/missconfig',
                'passing' => $host.'/ico/passed',
                'pending' => $host.'/ico/pending',
                'unknown' => $host.'/ico/unknown',
            ],
            'deployment@github' => [
                'list@get' => $host.'/deployment/list',
                'create@post' => $host.'/deployment/create',
                'createStatus@post' => $host.'/deployment/create/status',
            ],
        ];
    }

    @@Route('get', 'status')
    public function status(): void
    {
        \Response::redirect('https://status.khs1994.com');
    }

    @@Route('get', 'team')
    public function team(): void
    {
        \Response::redirect('https://github.com/pcit-ce/pcit/graphs/contributors');
    }

    @@Route('get', 'wechat')
    public function wechat(): void
    {
        \Response::redirect(
            'https://user-images.githubusercontent.com/16733187/49062650-de41ea00-f24f-11e8-9f22-99b5cd3d0195.jpg'
        );
    }

    @@Route('get', 'changelog')
    public function changelog(): void
    {
        \Response::redirect('https://github.com/pcit-ce/pcit/blob/master/CHANGELOG.md');
    }

    @@Route('get', 'donate')
    public function donate(): void
    {
        \Response::redirect('https://zan.khs1994.com');
    }

    @@Route('get', 'issues')
    public function issues(): void
    {
        \Response::redirect('https://github.com/pcit-ce/pcit/issues');
    }

    @@Route('get', 'support')
    public function support(): void
    {
        $this->issues();
    }

    @@Route('get', 'ce')
    public function ce(): void
    {
        \Response::redirect('https://docs.ci.khs1994.com/install/ce.html');
    }

    @@Route('get', 'ee')
    public function ee(): void
    {
        \Response::redirect('https://docs.ci.khs1994.com/install/ee.html');
    }

    @@Route('get', 'why')
    public function why(): void
    {
        \Response::redirect('https://docs.ci.khs1994.com/why.html');
    }

    @@Route('get', 'privacy-policy')
    public function privacy_policy(): void
    {
        \Response::redirect('https://docs.ci.khs1994.com/privacy-policy.html');
    }

    @@Route('get', 'terms-of-service')
    public function terms_of_service(): void
    {
        \Response::redirect('https://docs.ci.khs1994.com/terms-of-service.html');
    }

    @@Route('get', 'security')
    public function security(): void
    {
        \Response::redirect('https://docs.ci.khs1994.com/usage/security.html');
    }

    @@Route('get', 'api/v1')
    /**
     * Undocumented function
     *
     * @deprecated
     */
    public function v1(){
        return [];
    }

    @@Route('get', 'api/v1.1')
    @@\PCIT\Framework\Attributes\Deprecated
    public function v1_1(){
        return [];
    }

    @@Route('get', 'api/v1.2')
    public function v1_2(){
        return [];
    }
}
