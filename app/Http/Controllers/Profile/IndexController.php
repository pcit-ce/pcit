<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use PCIT\Support\Git;

class IndexController
{
    public function getClass(string $gitType)
    {
        $class = 'PCIT\\'.Git::getClassName($gitType).'\Profile\IndexController';

        return $class;
    }

    public function __invoke(string $gitType, string $username): void
    {
        $class = $this->getClass($gitType);
        (new $class())($username);
    }
}
