<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\CheckRunContext;
use PCIT\GPI\Webhooks\PustomizeHandler;

class CheckRunAbstract
{
    public function pustomize(CheckRunContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        (new PustomizeHandler())->handle('CheckRun', $context);
    }
}
