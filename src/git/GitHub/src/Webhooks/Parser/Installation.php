<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\InstallationContext;

class Installation
{
    public static function handle(string $webhooks_content): InstallationContext
    {
        return new InstallationContext([], $webhooks_content);
        //\Log::info('Receive event', ['type' => 'installation', 'action' => $installationContext->action]);
    }
}
