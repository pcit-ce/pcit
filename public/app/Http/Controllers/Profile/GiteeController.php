<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

class GiteeController extends GitHubController
{
    const TYPE = 'gitee';

    const TYPE_LOWER = 'gitee';

    public function __invoke(...$arg)
    {
        return parent::__invoke(...$arg);
    }
}
