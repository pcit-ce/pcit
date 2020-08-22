<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use PCIT\Framework\Attributes\Middleware;

class IndexController
{
    @@\Route('get', '{git_type}/{username}')
    @@Middleware('Logout')
    @@\Route('get', '{git_type}/{username}/{repo_name}')
    @@\Route('get', '{git_type}/{username}/{repo_name}/branches')
    @@\Route('get', '{git_type}/{username}/{repo_name}/builds')
    @@\Route('get', '{git_type}/{username}/{repo_name}/builds/{build_id}')
    @@\Route('get', '{git_type}/{username}/{repo_name}/jobs/{job_id}')
    @@\Route('get', '{git_type}/{username}/{repo_name}/pull_requests')
    @@\Route('get', '{git_type}/{username}/{repo_name}/settings')
    @@\Route('get', '{git_type}/{username}/{repo_name}/requests')
    @@\Route('get', '{git_type}/{username}/{repo_name}/caches')
    public function __invoke(...$arg): void
    {
        view('builds/index.html');
        exit;
    }
}
