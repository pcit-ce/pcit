<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\InstallationContext;
use PCIT\GPI\Webhooks\PustomizeHandler;

abstract class InstallationAbstract
{
    public function pustomize(InstallationContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        (new PustomizeHandler())->handle('Installation', $context);
    }
}
