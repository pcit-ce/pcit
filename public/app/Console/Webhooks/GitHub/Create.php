<?php

declare(strict_types=1);

namespace App\Console\Webhooks\GitHub;

use App\Repo;
use App\User;

class Create
{
    /**
     * Create "repository", "branch", or "tag".
     *
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
        ] = \KhsCI\Support\Webhooks\GitHub\Create::handle($json_content);

        User::updateUserInfo($account);
        User::updateInstallationId((int) $installation_id, $account->username);
        Repo::updateRepoInfo((int) $rid, $repo_full_name, null, null);

        if ('branch' === $ref_type) {
            $branch = $ref_type;
        } elseif ('repository' === $ref_type) {
            $repository = $ref_type;
        } elseif ('tag' === $ref_type) {
            $tag = $ref_type;
        }
    }
}
