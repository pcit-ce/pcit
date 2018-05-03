<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

class APIController
{
    public function __invoke()
    {
        $host = getenv('CI_HOST');

        return [
            'homepage' => $host,
            'login' => $host.'/login',
            'beta' => $host.'/beta',
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
            'webhoks@admin' => [
                'list@get' => [
                    'coding' => $host.'/webhooks/coding/{username}/{repo}',
                    'gitee' => $host.'/webhooks/gitee/{username}/{repo}',
                    'github' => $host.'/webhooks/github/{username}/{repo}',
                ],
                'cteate@post' => [
                    'coding' => $host.'/webhooks/coding/{username}/{repo}/{id}',
                    'gitee' => $host.'/webhooks/gitee/{username}/{repo}/{id}',
                    'github' => $host.'/webhooks/github/{username}/{repo}/{id}',
                ],
                'delete@delete' => [
                    'coding' => $host.'/webhooks/coding/{username}/{repo}/{id}',
                    'gitee' => $host.'/webhooks/gitee/{username}/{repo}/{id}',
                    'github' => $host.'/webhooks/github/{username}/{repo}/{id}',
                ],
                'activate@post' => [
                    'coding' => $host.'/webhooks/coding/{username}/{repo}/{id}/activate',
                    'gitee' => $host.'/webhooks/gitee/{username}/{repo}/{id}/activate',
                    'github' => $host.'/webhooks/github/{username}/{repo}/{id}/activate',
                ],
                'deactivate@post' => [
                    'coding' => $host.'/webhooks/coding/{username}/{repo}/{id}/deactivate',
                    'gitee' => $host.'/webhooks/gitee/{username}/{repo}/{id}/deactivate',
                    'github' => $host.'/webhooks/github/{username}/{repo}/{id}/deactivate',
                ],
            ],
            'webhooks@receive' => [
                'coding' => $host.'/webhooks/coding',
                'gitee' => $host.'/webhooks/gitee',
                'github' => $host.'/webhooks/github',
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
            ],
            'sync@post' => [
                'coding' => $host.'/sync/coding',
                'gitee' => $host.'/sync/gitee',
                'github' => $host.'/sync/github',
            ],
            'profile' => [
                'coding' => $host.'/profile/coding/{username}',
                'gitee' => $host.'/profile/gitee/{username}',
                'github' => $host.'/profile/github/{username}',
            ],
            'statuses@github' => [
                'list@get' => $host.'/status/github/{username}/{repo}/{ref}',
                'combinedStatus@get' => $host.'/combined_status/github/{username}/{repo}/{ref}',
            ],
            'ico' => [
                'canceled' => $host.'/ico/canceled',
                'errored' => $host.'/ico/errored',
                'failing' => $host.'/ico/failing',
                'passing' => $host.'/ico/passing',
                'pending' => $host.'/ico/pending',
            ],
            'deployment@github' => [
                'list@get' => $host.'/deployment/list',
                'create@post' => $host.'/deployment/create',
                'createStatus@post' => $host.'/deployment/create/status',
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
