<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

class APIController
{
    public function __invoke()
    {
        $host = getenv('CI_HOST');
        return [
            'tests' => [
                'route not found' => $host.'/not_exists_url',
            ],
            'oauth' => [
                'coding' => $host.'/oauth/coding/login',
                'gitee' => $host.'/oauth/gitee/login',
                'github' => $host.'/oauth/github/login',
            ],
            'webhoks@admin' => [
                'list@get' => [
                    'coding' => $host.'/webhooks/list/coding/{user}/{repo}',
                    'gitee' => $host.'/webhooks/list/gitee/{user}/{repo}',
                    'github' => $host.'/webhooks/list/github/{user}/{repo}',
                ],
                'cteate@post' => [
                    'coding' => $host.'/webhooks/create/coding/{user}/{repo}/{id}',
                    'gitee' => $host.'/webhooks/create/gitee/{user}/{repo}/{id}',
                    'github' => $host.'/webhooks/create/github/{user}/{repo}/{id}',
                ],
                'delete@delete' => [
                    'coding' => $host.'/webhooks/delete/coding/{user}/{repo}/{id}',
                    'gitee' => $host.'/webhooks/delete/gitee/{user}/{repo}/{id}',
                    'github' => $host.'/webhooks/delete/github/{user}/{repo}/{id}',
                ],
            ],
            'webhooks@receive' => [
                'coding' => $host.'/webhooks/coding',
                'gitee' => $host.'/webhooks/gitee',
                'github' => $host.'/webhooks/github',
            ],
            'repo' => [
                'main' => $host.'/{git_type}/{user}/{repo}',
                'branches' => $host.'/{git_type}/{user}/{repo}/branches',
                'builds' => [
                    'main' => $host.'/{git_type}/{user}/{repo}/builds',
                    'id' => $host.'/{git_type}/{user}/{repo}/builds/{id}',
                ],
                'pull_requests' => $host.'/{git_type}/{user}/{repo}/pull_requests',
                'settings' => $host.'/{git_type}/{user}/{repo}/settings',
                'requests' => $host.'/{git_type}/{user}/{repo}/requests',
                'caches' => $host.'/{git_type}/{user}/{repo}/caches',
            ],
            'sync@post' => [
                'coding' => $host.'/sync/coding',
                'gitee' => $host.'/sync/gitee',
                'github' => $host.'/sync/github',
            ],
            'queue' => [
                'coding' => '',
                'gitee' => '',
                'github' => '',
            ],
            'profile' => [
                'coding' => $host.'/profile/coding/{user_org}',
                'gitee' => $host.'/profile/gitee/{user_org}',
                'github' => $host.'/profile/github/{user_org}',
            ],
            'dashboard' => $host.'/dashboard',
            'api' => $host.'/api',
            'about' => $host.'/about',
            'team' => $host.'/team',
            'blog' => $host.'/blog',
            'status' => $host.'/status',
            'feedback' => 'https://github.com/khs1994-php/khsci/issues',
        ];
    }

    public function __call($name, $arguments): void
    {
        var_dump($name, $arguments);
    }
}
