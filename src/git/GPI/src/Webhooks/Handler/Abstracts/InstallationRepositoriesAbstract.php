<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\InstallationRepositoriesContext;
use PCIT\GPI\Webhooks\Handler\Handler;

abstract class InstallationRepositoriesAbstract extends Handler
{
    public function pustomize(InstallationRepositoriesContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        $this->callPustomize('InstallationRepositories', $context);
    }
}
