<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Abstracts\PingAbstract;

class Ping extends PingAbstract
{
    public function handle(string $webhooks_content): void
    {
    }
}
