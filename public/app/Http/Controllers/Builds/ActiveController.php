<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

class ActiveController
{
    /**
     * Returns a list of "active" builds for the owner.
     *
     * /owner/{git_type}/{username}/active
     *
     * @param array $args
     */
    public function __invoke(...$args): void
    {
        list($git_type, $username) = $args;
    }
}
