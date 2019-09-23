<?php

declare(strict_types=1);

namespace PCIT\GitHub\WebhooksParse;

use PCIT\Framework\Support\Log;

class Ping
{
    /**
     * @param $json_content
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function handle($json_content)
    {
        Log::debug(__FILE__, __LINE__, 'receive ping event', [], Log::INFO);

        $obj = json_decode($json_content);

        $rid = $obj->repository->id ?? 0;

        $event_time = time();

        return [
            'rid' => $rid,
            'event_time' => $event_time,
        ];
    }
}
