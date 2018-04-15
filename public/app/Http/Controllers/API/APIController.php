<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use KhsCI\Support\Response;

class APIController
{
    public function __invoke(): void
    {
        $host = getenv('CI_HOST');
        Response::json([
            'oauth' => [
                'coding' => $host.'/oauth/coding/login',
                'gitee' => $host.'/oauth/gitee/login',
                'github' => $host.'/oauth/github/login',
            ],
            'webhoks@admin' => [
                'add@post' => $host.'/webhooks/add/{git_type}/{user}/{repo}',
                'list@get' => $host.'/webhooks/list/{git_type}/{user}/{repo}',
                'delete@delete' => $host.'/webhooks/delete/{git_type}/{user}/{repo}',
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
        ]);
    }

    public function __call($name, $arguments): void
    {
        var_dump($name, $arguments);
    }
}
