<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\InstallationContext;
use PCIT\GPI\Webhooks\Context\InstallationRepositoriesContext;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Sender;

class Installation
{
    public static function handle(string $webhooks_content): InstallationContext
    {
        $obj = json_decode($webhooks_content);

        $action = $obj->action;

        \Log::info('Receive event', ['type' => 'installation', 'action' => $action]);

        $installation = $obj->installation;

        $installation_id = $installation->id;

        $account = $installation->account;

        $repo = $obj->repositories ?? null;

        $org = 'Organization' === $account->type;

        $installationContext = new InstallationContext([], $webhooks_content);
        $installationContext->installation_id = $installation_id;
        $installationContext->action = $action;
        $installationContext->repo = $repo;
        // sender 可视为管理员
        $installationContext->sender = new Sender($obj->sender);
        $installationContext->account = new Account($account, $org);

        return $installationContext;
    }

    public static function repositories(string $webhooks_content): InstallationRepositoriesContext
    {
        $obj = json_decode($webhooks_content);

        $action = $obj->action;

        \Log::info('Receive event', ['type' => 'installation_repositories', 'action' => $action]);

        $installation = $obj->installation;

        $installation_id = $installation->id;

        $account = $installation->account;

        $repo_type = 'repositories_'.$action;

        $repo = $obj->$repo_type;

        $org = 'Organization' === $account->type;

        $irc = new InstallationRepositoriesContext([], $webhooks_content);

        $irc->installation_id = $installation_id;
        $irc->action = $action;
        $irc->repo = $repo;
        $irc->sender = new Sender($obj->sender);
        $irc->account = new Account($account, $org);

        return $irc;
    }
}
