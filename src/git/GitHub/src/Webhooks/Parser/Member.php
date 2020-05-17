<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\MemberContext;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;

class Member
{
    public static function handle(string $webhooks_content): MemberContext
    {
        $obj = json_decode($webhooks_content);

        $action = $obj->action;

        \Log::info('Receive member event', [
            'type' => 'member',
            'action' => $action,
        ]);

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

        \Log::info("$action $rid $member_uid", []);

        $org = ($obj->organization ?? false) ? true : false;

        $memberContext = new MemberContext([], $webhooks_content);

        $memberContext->action = $action;
        $memberContext->installation_id = $installation_id;
        $memberContext->rid = $rid;
        $memberContext->repo_full_name = $repo_full_name;
        $memberContext->member_uid = $member_uid;
        $memberContext->member_username = $member_username;
        $memberContext->member_pic = $member_pic;
        $memberContext->account = (new Account($repository_owner, $org));

        return $memberContext;
    }
}
