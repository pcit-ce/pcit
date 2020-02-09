<?php

declare(strict_types=1);

namespace PCIT\Provider\AliyunDockerRegistry;

use Exception;

/**
 * 接收 Webhooks.
 */
class WebhooksServer
{
    /**
     * @throws Exception
     */
    public function server(): void
    {
        \Log::info('receive aliyun_docker_registry webhooks');

        $request = app('request');

        // $content = file_get_contents('php://input');
        $content = $request->getContent();

        \Cache::store()->lpush('/pcit/webhooks', json_encode(['aliyun_docker_registry', 'push', $content]));
    }
}
