<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GitHub\Webhooks\Parser\UserBasicInfo\Account;
use PCIT\GitHub\Webhooks\Parser\UserBasicInfo\Sender;

class Installation
{
    public static function handle(string $webhooks_content): array
    {
        $obj = json_decode($webhooks_content);

        $action = $obj->action;

        \Log::info('Receive event', ['type' => 'installation', 'action' => $action]);

        $installation = $obj->installation;

        $installation_id = $installation->id;

        $account = $installation->account;

        $repo = $obj->repositories ?? null;

        $org = 'Organization' === $account->type;

        return [
            'installation_id' => $installation_id,
            'action' => $action,
            'repo' => $repo,
            // sender 可视为管理员
            'sender' => (new Sender($obj->sender)),
            'account' => (new Account($account, $org)),
        ];
    }

    public static function repositories(string $webhooks_content): array
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

        return [
            'installation_id' => $installation_id,
            'action' => $action,
            'repo' => $repo,
            'sender' => (new Sender($obj->sender)),
            'account' => (new Account($account, $org)),
        ];
    }
}
