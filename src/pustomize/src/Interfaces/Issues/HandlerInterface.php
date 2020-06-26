<?php

declare(strict_types=1);

namespace PCIT\Pustomize\Interfaces\Issues;

use PCIT\GPI\Webhooks\Context\IssuesContext;

interface HandlerInterface
{
    /**
     * 当 issue opened 时调用.
     */
    public function handle(IssuesContext $context);
}
