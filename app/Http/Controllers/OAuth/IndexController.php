<?php

declare(strict_types=1);

namespace App\Http\Controllers\OAuth;

class IndexController
{
    public function getClass($gitType)
    {
        switch ($gitType) {
            case 'github':
                $class = 'GitHub';
                break;

            default:
                $class = ucfirst($gitType);
                break;
        }

        $class = 'PCIT\\'.$class.'\OAuth\IndexController';

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
