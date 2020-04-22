<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use App\Build;

class Ping
{
    /**
     * @return string
     *
     * @throws \Exception
     */
    public static function handle(string $webhooks_content)
    {
        [
            'rid' => $rid,
            'created_at' => $created_at
        ] = \PCIT\GitHub\Webhooks\Parser\Ping::handle($webhooks_content);

        return Build::insertPing('github', $rid, $created_at);
    }
}
