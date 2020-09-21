<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\CheckSuiteContext;
use PCIT\GPI\Webhooks\Handler\Handler;

abstract class CheckSuiteAbstract extends Handler
{
    public function pustomize(CheckSuiteContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        $this->callPustomize('CheckSuite', $context);
    }
}
