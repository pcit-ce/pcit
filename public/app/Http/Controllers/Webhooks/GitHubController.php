<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use Exception;
use KhsCI\Service\Webhooks\GitHub;

class GitHubController
{
    /**
     * @return array
     *
     * @throws Exception
     */
    public function __invoke()
    {
        $github = new GitHub();

        return $github();
    }
}
