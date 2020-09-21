<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler;

use PCIT\GPI\Webhooks\ContextInterface;
use PCIT\GPI\Webhooks\PustomizeHandler;

abstract class Handler
{
    public function callPustomize(string $type, ContextInterface $context): void
    {
        (new PustomizeHandler())->handle($type, $context);
    }
}
