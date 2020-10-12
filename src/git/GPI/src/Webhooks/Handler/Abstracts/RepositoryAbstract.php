<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\RepositoryContext;
use PCIT\GPI\Webhooks\Handler\Handler;

abstract class RepositoryAbstract extends Handler
{
    public function pustomize(RepositoryContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        $this->callPustomize('Repository', $context);
    }
}
