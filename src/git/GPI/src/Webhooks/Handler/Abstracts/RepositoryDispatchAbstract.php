<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\RepositoryDispatchContext;
use PCIT\GPI\Webhooks\Handler\Handler;

abstract class RepositoryDispatchAbstract extends Handler
{
    public function pustomize(RepositoryDispatchContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        $this->callPustomize('RepositoryDispatch', $context);
    }
}
