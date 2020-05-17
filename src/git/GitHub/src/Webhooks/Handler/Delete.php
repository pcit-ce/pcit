<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

class Delete
{
    /**
     * @throws \Exception
     */
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Delete::handle($webhooks_content);

        $installation_id = $context->installation_id;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $ref_type = $context->ref_type;
        $account = $context->account;
        $ref = $context->ref;

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name))
            ->handle();

        if ('branch' === $ref_type) {
            \App\Build::deleteByBranch($ref, (int) $rid, 'github');
        }
    }
}
