<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\IssuesContext;
use PCIT\GPI\Webhooks\Handler\Interfaces\IssuesInterface;
use PCIT\GPI\Webhooks\PustomizeHandler;

abstract class IssuesAbstract implements IssuesInterface
{
    public function pustomize(IssuesContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        (new PustomizeHandler())->handle('Issues', $context);
    }
}
