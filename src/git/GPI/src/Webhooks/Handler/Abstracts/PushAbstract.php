<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\PushContext;
use PCIT\GPI\Webhooks\Handler\Handler;
use PCIT\GPI\Webhooks\Handler\Interfaces\PushInterface;

abstract class PushAbstract extends Handler implements PushInterface
{
    public function pustomize(PushContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        $this->callPustomize('Push', $context);
    }
}
