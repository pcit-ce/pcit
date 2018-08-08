<?php

declare(strict_types=1);

namespace App\Console\Webhooks\GitHub;

use App\Repo;

class Member
{
    /**
     * @param $json_content
     *
     * @throws \Exception
     */
    public static function handle($json_content): void
    {
        [
            'action' => $action,
            'rid' => $rid,
            'member_uid' => $member_uid
        ] = \KhsCI\Support\Webhooks\GitHub\Member::handle($json_content);

        'added' === $action && Repo::updateAdmin((int) $rid, (int) $member_uid);
        'removed' === $action && Repo::deleteAdmin((int) $rid, (int) $member_uid);
    }
}
