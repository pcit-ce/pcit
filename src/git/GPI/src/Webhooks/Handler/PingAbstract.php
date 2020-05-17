<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Interfaces\PingInterface;

class PingAbstract implements PingInterface
{
    /**
     * @throws \Exception
     */
    public function handle(string $webhooks_content): void
    {
        \Log::info('receive ping event');
    }
}
