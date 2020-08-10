<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\MemberContext;
use PCIT\GPI\Webhooks\PustomizeHandler;

abstract class MemberAbstract
{
    public function pustomize(MemberContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        (new PustomizeHandler())->handle('Member', $context);
    }
}
