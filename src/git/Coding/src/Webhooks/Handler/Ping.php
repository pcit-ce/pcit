<?php

declare(strict_types=1);

namespace PCIT\Coding\Webhooks\Handler;

class Ping
{
    /**
     * @return string
     *
     * @throws \Exception
     */
    public function handle(string $webhooks_content)
    {
        \Log::error('receive ping event');
    }
}
