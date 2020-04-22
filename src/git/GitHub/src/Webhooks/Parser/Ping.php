<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

class Ping
{
    public static function handle(string $webhooks_content): array
    {
        \Log::info('receive ping event', []);

        $obj = json_decode($webhooks_content);

        $rid = $obj->repository->id ?? 0;

        $event_time = time();

        return [
            'rid' => $rid,
            'event_time' => $event_time,
        ];
    }
}
