<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\IssuesContext;
use PCIT\GPI\Webhooks\Handler\Handler;
use PCIT\GPI\Webhooks\Handler\Interfaces\IssuesInterface;

abstract class IssuesAbstract extends Handler implements IssuesInterface
{
    public function pustomize(IssuesContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        $this->callPustomize('Issues', $context);
    }
}
