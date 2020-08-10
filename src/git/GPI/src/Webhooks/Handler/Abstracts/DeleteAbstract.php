<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\DeleteContext;
use PCIT\GPI\Webhooks\PustomizeHandler;

abstract class DeleteAbstract
{
    public function pustomize(DeleteContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        (new PustomizeHandler())->handle('Delete', $context);
    }
}
