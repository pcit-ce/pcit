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
     * PATCH.
     *
     * /repo/{username}/{repo.name}/issues/translate/{issue.number}
     *
     * @param mixed ...$args
     */
    public function translate(...$args): void
    {
        list($username, $repo, $issue_number) = $args;

        JWTController::checkByRepo($username, $repo);

        \PCIT\GitHub\Webhooks\Handler\Issues::translateTitle(
            $username.'/'.$repo,
            (int) $issue_number,
            null,
            null
        );
    }
}
