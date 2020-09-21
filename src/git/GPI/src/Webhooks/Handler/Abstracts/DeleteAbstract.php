<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\DeleteContext;
use PCIT\GPI\Webhooks\Handler\Handler;

abstract class DeleteAbstract extends Handler
{
    public function pustomize(DeleteContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        $this->callPustomize('Delete', $context);
    }
}
