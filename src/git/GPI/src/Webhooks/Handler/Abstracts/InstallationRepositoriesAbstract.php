<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\InstallationRepositoriesContext;
use PCIT\GPI\Webhooks\PustomizeHandler;

abstract class InstallationRepositoriesAbstract
{
    public function pustomize(InstallationRepositoriesContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        (new PustomizeHandler())->handle('InstallationRepositories', $context);
    }
}
