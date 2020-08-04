<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\Components\User\Account;
use PCIT\GPI\Webhooks\Context\Components\User\Sender;
use PCIT\GPI\Webhooks\Context\InstallationRepositoriesContext;

class InstallationRepositories
{
    public static function handle(string $webhooks_content): InstallationRepositoriesContext
    {
        $obj = json_decode($webhooks_content);

        $action = $obj->action;

        \Log::info('Receive event', ['type' => 'installation_repositories', 'action' => $action]);

        $installation = $obj->installation;

        $installation_id = $installation->id;

        $account = $installation->account;

        $repo_type = 'repositories_'.$action;

        $repositories = $obj->$repo_type;

        $org = 'Organization' === $account->type;

        $irc = new InstallationRepositoriesContext([], $webhooks_content);

        $irc->installation_id = $installation_id;
        $irc->action = $action;
        $irc->repositories = $repositories;
        $irc->sender = new Sender($obj->sender);
        $irc->account = new Account($account, $org);

        return $irc;
    }
}
