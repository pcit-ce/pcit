<?php

declare(strict_types=1);

namespace App\Console\Webhooks\GitHub;

use KhsCI\Support\DB;

class Delete
{
    /**
     * @param $json_content
     *
     * @throws \Exception
     */
    public static function handle($json_content): void
    {
        [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'ref_type' => $ref_type,
            'account' => $account,
        ] = \KhsCI\Support\Webhooks\GitHub\Delete::handle($json_content);

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name))
            ->handle();

        if ('branch' === $ref_type) {
            $sql = 'DELETE FROM builds WHERE git_type=? AND branch=? AND rid=?';

            DB::delete($sql, ['github', $ref_type, (int) $rid]);
        }
    }
}
