<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\MemberContext;

class Member
{
    public static function handle(string $webhooks_content): MemberContext
    {
        $memberContext = new MemberContext([], $webhooks_content);

        $action = $memberContext->action;

        \Log::info('Receive member event', [
            'type' => 'member',
            'action' => $action,
        ]);

        $member = $memberContext->member;
        $member_username = $member->login;
        $member_uid = $member->id;
        $member_pic = $member->avatar_url;

        $repository = $memberContext->repository;
        \Log::info("$action ".$repository->id." $member_uid", []);

        $memberContext->member_uid = $member_uid;
        $memberContext->member_username = $member_username;
        $memberContext->member_pic = $member_pic;
        $memberContext->owner = $repository->owner;

        return $memberContext;
    }
}
