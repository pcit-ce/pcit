<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Handler\Interfaces\PushInterface;
use PCIT\GPI\Webhooks\PustomizeHandler;

abstract class PushAbstract implements PushInterface
{
    public function pustomize(Context $context, string $git_type): void
    {
        $context->git_type = $git_type;

        (new PustomizeHandler())->handle('Push', $context);
    }
}
