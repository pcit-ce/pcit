<?php

declare(strict_types=1);

namespace PCIT\Coding\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\PushAbstract;

class Push extends PushAbstract
{
    public function handle(string $webhooks_content): void
    {
    }

    public function tag(array $content): void
    {
    }
}
