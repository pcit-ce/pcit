<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

class StarController
{
    /**
     * star a repository based on the currently logged in user.
     *
     * post
     *
     * /repo/{repository.id}/star
     */
    public function __invoke(): void
    {
    }

    /**
     * unstar a repository based on the currently logged in user.
     *
     * post
     *
     * /repo/{repository.slug}/unstar
     */
    public function unStar(): void
    {
    }
}
