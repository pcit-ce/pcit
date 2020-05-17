<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use App\Repo;

class Member
{
    /**
     * `added` `deleted` `edited` `removed`.
     *
     * @throws \Exception
     */
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Member::handle($webhooks_content);

        $action = $context->action;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $member_uid = $context->member_uid;

        'added' === $action && Repo::updateAdmin((int) $rid, (int) $member_uid);
        'removed' === $action && Repo::deleteAdmin((int) $rid, (int) $member_uid);
    }
}
