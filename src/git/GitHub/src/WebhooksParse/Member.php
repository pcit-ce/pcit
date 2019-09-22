<?php

declare(strict_types=1);

namespace PCIT\GitHub\WebhooksParse;

use PCIT\GitHub\WebhooksParse\UserBasicInfo\Account;
use PCIT\Support\Log;

class Member
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

        Log::debug(__FILE__, __LINE__, 'Receive member event is '.$action, [], Log::INFO);

        $member = $obj->member;
        $member_username = $member->login;
        $member_uid = $member->id;
        $member_pic = $member->avatar_url;

        $repository = $obj->repository;

        $rid = $repository->id;
        $repo_full_name = $repository->full_name;

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        $installation_id = $obj->installation->id ?? null;

        Log::debug(null, null, "$action $rid $member_uid", [], Log::INFO);

        $org = ($obj->organization ?? false) ? true : false;

        return [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'member_uid' => $member_uid,
            'member_username' => $member_username,
            'member_pic' => $member_pic,
            'account' => (new Account($repository_owner, $org)),
        ];
    }
}
