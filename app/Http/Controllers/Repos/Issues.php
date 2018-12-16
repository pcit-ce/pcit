<?php

declare(strict_types=1);

namespace App\Http\Controllers\Repos;

use App\Http\Controllers\Users\JWTController;

/**
 * Issue.
 */
class Issues
{
    /**
     * @param mixed ...$args
     *
     * @throws \Exception
     */
    public function translate(...$args): void
    {
        list($username, $repo, $issue_number) = $args;

        JWTController::checkByRepo($username, $repo);

        \App\Console\Webhooks\GitHub\Issues::translateTitle(
            $username.'/'.$repo, (int) $issue_number, null, null);
    }
}
