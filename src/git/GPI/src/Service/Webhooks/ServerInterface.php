<?php

declare(strict_types=1);

namespace PCIT\GPI\Service\Webhooks;

interface ServerInterface
{
    public function server();

    public function secret(string $content);
}
