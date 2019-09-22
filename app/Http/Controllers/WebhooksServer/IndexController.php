<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

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

        $class = 'PCIT\\'.$class.'\WebhooksServer\IndexController';

        return $class;
    }

    public function __invoke($gitType)
    {
        $class = $this->getClass($gitType);

        return (new $class())();
    }
}
