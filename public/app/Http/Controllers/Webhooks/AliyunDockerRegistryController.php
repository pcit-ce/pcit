<?php

namespace App\Http\Controllers\Webhooks;

use Exception;
use KhsCI\Support\Cache;

class AliyunDockerRegistryController
{
    /**
     * @throws Exception
     */
    public function __invoke()
    {
        $content = file_get_contents('php://input');

        Cache::connect()->lpush('webhooks', json_encode(['aliyun_docker_registry', 'push', $content]));
    }
}
