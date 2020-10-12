<?php

declare(strict_types=1);

namespace PCIT\Pustomize\Member;

use App\Repo;
use PCIT\GPI\Webhooks\Context\MemberContext;

class Handler
{
    public function handle(MemberContext $context): void
    {
        $repository = $context->repository;
        $action = $context->action;
        $rid = $repository->id;
        $repo_full_name = $repository->full_name;
        $member_uid = $context->member_uid;
        $git_type = $context->git_type;

        'added' === $action && Repo::updateAdmin((int) $rid, (int) $member_uid, $git_type);
        'removed' === $action && Repo::deleteAdmin((int) $rid, (int) $member_uid, $git_type);
    }
}
