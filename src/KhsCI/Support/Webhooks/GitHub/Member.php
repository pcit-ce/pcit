<?php

declare(strict_types=1);

namespace KhsCI\Support\Webhooks\GitHub;

use KhsCI\Support\Log;

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

        $installation_id = $obj->installation->id ?? null;

        Log::debug(null, null, "$action $rid $member_uid", [], Log::INFO);

        return [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'member_uid' => $member_uid,
        ];
    }
}
