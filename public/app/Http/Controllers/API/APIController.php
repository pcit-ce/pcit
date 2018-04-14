<?php

namespace App\Http\Controllers\API;

use KhsCI\Support\Response;

class APIController
{
    public function __invoke()
    {
        $host = getenv('CI_HOST');
        Response::json(["oauth" => [
            "gitee" => $host.'/oauth/gitee/login',
            "coding" => $host.'/oauth/coding/login',
            "github" => $host.'/oauth/github/login',
        ],
            "webhooks" => [
                "gitee" => $host.'/webhooks/gitee',
                "coding" => $host.'/webhooks/coding',
                "github" => $host.'/webhooks/github',
            ],
            "repo" => [
                "main" => $host.'/{git_type}/{user}/{repo}',
                "branches" => $host.'/{git_type}/{user}/{repo}/branches',
                "builds" => [
                    "main" => $host."/{git_type}/{user}/{repo}/builds",
                    "id" => $host."/{git_type}/{user}/{repo}/builds/{id}",
                ],
                "pull_requests" => $host."/{git_type}/{user}/{repo}/builds",
                "settings" => $host."/{git_type}/{user}/{repo}/settings",
                "caches" => $host."/{git_type}/{user}/{repo}/caches",
            ],
            "queue" => '',
            "dashboard" => $host.'/dashboard',
            "api" => $host.'/api',
            "about" => $host.'/about',
            "feedback" => 'https://github.com/khs1994-php/khsci/issues',
            "team" => $host.'/team',
            "blog" => $host.'/blog',
            "status" => $host.'/status',
        ]);
    }

    public function __call($name, $arguments)
    {
        var_dump($name, $arguments);
    }
}