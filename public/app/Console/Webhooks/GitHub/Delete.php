<?php

declare(strict_types=1);

namespace App\Console\Webhooks\GitHub;

use App\Repo;
use App\User;
use KhsCI\Support\DB;

class Delete
{
    /**
     * @throws \Exception
     */
    public static function handle($json_content): void
    {
        [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'username' => $username,
            'ref_type' => $ref_type,
        ] = \KhsCI\Support\Webhooks\GitHub\Delete::handle($json_content);

        Repo::updateGitHubInstallationIdByRid('github', (int) $rid, $repo_full_name, (int) $installation_id);
        User::updateInstallationId('github', (int) $installation_id, $username);

        if ('branch' === $ref_type) {
            $sql = 'DELETE FROM builds WHERE git_type=? AND branch=? AND rid=?';

            DB::delete($sql, ['github', $obj->ref, (int) $rid]);
        }
    }
}
