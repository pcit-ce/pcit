<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Handler\Handler;
use PCIT\GPI\Webhooks\Handler\Interfaces\PingInterface;

abstract class PingAbstract extends Handler implements PingInterface
{
    /**
     * @param mixed $context
     *
     * @throws \Exception
     */
    public function pustomize($context, string $git_type): void
    {
        $context->git_type = $git_type;

        $this->callPustomize('Ping', $context);
    }
}
