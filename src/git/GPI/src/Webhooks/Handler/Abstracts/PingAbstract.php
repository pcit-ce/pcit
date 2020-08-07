<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Handler\Interfaces\PingInterface;
use PCIT\GPI\Webhooks\PustomizeHandler;

abstract class PingAbstract implements PingInterface
{
    /**
     * @param mixed $context
     *
     * @throws \Exception
     */
    public function pustomize($context, string $git_type): void
    {
        $context->git_type = $git_type;

        (new PustomizeHandler())->handle('ping', $context);
    }
}
