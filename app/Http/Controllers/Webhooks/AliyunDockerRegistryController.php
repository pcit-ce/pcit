<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use Exception;
use PCIT\Support\Cache;

class AliyunDockerRegistryController
{
    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        $request = app('request');

        // $content = file_get_contents('php://input');
        $content = $request->getContent();

        Cache::store()->lpush('/pcit/webhooks', json_encode(['aliyun_docker_registry', 'push', $content]));
    }
}
