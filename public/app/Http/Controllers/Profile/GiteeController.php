<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

class GiteeController extends GitHubController
{
    const TYPE = 'gitee';

    public function __invoke(...$arg): void
    {
        parent::__invoke(...$arg);
    }
}
