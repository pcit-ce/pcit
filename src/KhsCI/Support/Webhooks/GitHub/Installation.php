<?php

declare(strict_types=1);

namespace KhsCI\Support\Webhooks\GitHub;

use KhsCI\Support\Log;

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
        $account = $account->login;
        $account_uid = $account->id;
        $account_pic = $account->avatar_url;

        $sender = $obj->sender;
        $sender_username = $sender->login;
        $sender_uid = $sender->id;
        $sender_pic = $sender->avatar_url;

        $org = 'Organization' === $account->type;

        return [
            'installation_id' => $installation_id,
            'account' => $account,
            'account_uid' => $account_uid,
            'account_pic' => $account_pic,
            'org' => $org,
            'sender_username' => $sender_username,
            'sender_uid' => $sender_uid,
            'sender_pic' => $sender_pic,
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
        $account = $account->login;
        $account_uid = $account->id;
        $account_pic = $account->avatar_url;

        $sender = $obj->sender;
        $sender_username = $sender->login;
        $sender_uid = $sender->id;
        $sender_pic = $sender->avatar_url;

        $repo_type = 'repositories_'.$action;

        $repo = $obj->$repo_type;

        $org = 'Organization' === $account->type;

        return [
            'installation_id' => $installation_id,
            'repo' => $repo,
            'account' => $account,
            'account_uid' => $account_uid,
            'account_pic' => $account_pic,
            'org' => $org,
            'sender_username' => $sender_username,
            'sender_uid' => $sender_uid,
            'sender_pic' => $sender_pic,
        ];
    }
}
