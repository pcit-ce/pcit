<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Interfaces\PingInterface;

class Ping implements PingInterface
{
    /**
     * @return string
     *
     * @throws \Exception
     */
    public function handle(string $webhooks_content)
    {
    }
}
