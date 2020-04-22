<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

class Delete
{
    /**
     * @throws \Exception
     */
    public static function handle(string $webhooks_content): void
    {
        [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'ref_type' => $ref_type,
            'account' => $account,
            'ref' => $ref,
        ] = \PCIT\GitHub\Webhooks\Parser\Delete::handle($webhooks_content);

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name))
            ->handle();

        if ('branch' === $ref_type) {
            \App\Build::deleteByBranch($ref, (int) $rid, 'github');
        }
    }
}
