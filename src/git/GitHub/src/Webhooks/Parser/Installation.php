<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\Components\User\Account;
use PCIT\GPI\Webhooks\Context\Components\User\Sender;
use PCIT\GPI\Webhooks\Context\InstallationContext;

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

        $repositories = $obj->repositories ?? null;

        $org = 'Organization' === $account->type;

        $installationContext = new InstallationContext([], $webhooks_content);
        $installationContext->installation_id = $installation_id;
        $installationContext->action = $action;
        $installationContext->repositories = $repositories;
        // sender 可视为管理员
        $installationContext->sender = new Sender($obj->sender);
        $installationContext->account = new Account($account, $org);

        return $installationContext;
    }
}
