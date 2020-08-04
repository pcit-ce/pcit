<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\Components\Repository;
use PCIT\GPI\Webhooks\Context\Components\User\Owner;
use PCIT\GPI\Webhooks\Context\MemberContext;

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

        $repository = new Repository($obj->repository);
        $org = ($obj->organization ?? false) ? true : false;
        $repository->owner = new Owner($repository->owner, $org);

        $installation_id = $obj->installation->id ?? null;

        \Log::info("$action ".$repository->rid." $member_uid", []);

        $memberContext = new MemberContext([], $webhooks_content);

        $memberContext->action = $action;
        $memberContext->installation_id = $installation_id;
        $memberContext->rid = $repository->id;
        $memberContext->repo_full_name = $repository->full_name;
        $memberContext->member_uid = $member_uid;
        $memberContext->member_username = $member_username;
        $memberContext->member_pic = $member_pic;
        $memberContext->owner = $repository->owner;

        return $memberContext;
    }
}
