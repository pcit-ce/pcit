<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\InstallationRepositoriesContext;

class InstallationRepositories
{
    public static function handle(string $webhooks_content): InstallationRepositoriesContext
    {
        $irc = new InstallationRepositoriesContext([], $webhooks_content);

        \Log::info('Receive event', ['type' => 'installation_repositories', 'action' => $irc->action]);

        return $irc;
    }
}
