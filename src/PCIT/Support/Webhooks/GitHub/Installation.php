<?php

declare(strict_types=1);

namespace PCIT\Support\Webhooks\GitHub;

use PCIT\Support\Log;
use PCIT\Support\Webhooks\GitHub\UserBasicInfo\Account;
use PCIT\Support\Webhooks\GitHub\UserBasicInfo\Sender;

class Installation
{
    /**
     * @param $json_content
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function handle($json_content)
    {
        $obj = json_decode($json_content);

        $action = $obj->action;

        Log::debug(null, null, 'Receive event', ['installation' => $action], Log::INFO);

        $installation = $obj->installation;

        $installation_id = $installation->id;

        $account = $installation->account;

        $org = 'Organization' === $account->type;

        return [
            'installation_id' => $installation_id,
            'action' => $action,
            // sender 可视为管理员
            'sender' => (new Sender($obj->sender)),
            'account' => (new Account($account, $org)),
        ];
    }

    /**
     * @param $json_content
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function repositories($json_content)
    {
        $obj = json_decode($json_content);

        $action = $obj->action;

        Log::debug(null, null, 'Receive event', ['installation_repositories' => $action], Log::INFO);

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
