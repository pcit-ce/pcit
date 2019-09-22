<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

class IndexController
{
    public function getClass(string $gitType)
    {
        switch ($gitType) {
            case 'github':
                $class = 'GitHub';
                break;

            default:
                $class = ucfirst($gitType);
                break;
        }

        $class ='PCIT\\'.$class.'\Profile\IndexController';

        return $class;
    }

    public function __invoke(string $gitType, string $username): void
    {
        $class = $this->getClass($gitType);
        (new $class())($username);
    }
}
