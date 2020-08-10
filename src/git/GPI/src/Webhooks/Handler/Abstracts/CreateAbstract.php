<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\CreateContext;
use PCIT\GPI\Webhooks\PustomizeHandler;

abstract class CreateAbstract
{
    public function pustomize(CreateContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        (new PustomizeHandler())->handle('Create', $context);
    }
}
