<?php

declare(strict_types=1);

namespace App\Http\Controllers\OAuth;

use PCIT\Support\Git;

class IndexController
{
    public function getClass($gitType)
    {
        $class = 'PCIT\\'.Git::getClassName($gitType).'\OAuth\IndexController';

        return $class;
    }

    public function getLoginUrl(string $gitType)
    {
        $class = $this->getClass($gitType);

        return (new $class())->getLoginUrl();
    }

    public function getAccessToken(string $gitType): void
    {
        $class = $this->getClass($gitType);

        (new $class())->getAccessToken();
    }
}
