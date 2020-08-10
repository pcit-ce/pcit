<?php

declare(strict_types=1);

namespace PCIT\Pustomize\Delete;

use PCIT\GPI\Webhooks\Context\DeleteContext;
use PCIT\Subject;
use PCIT\UpdateUserInfo;

class Handler
{
    public function handle(DeleteContext $context): void
    {
        $installation_id = $context->installation_id;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $ref_type = $context->ref_type;
        $owner = $context->owner;
        $ref = $context->ref;
        $default_branch = $context->repository->default_branch;
        $git_type = $context->git_type;

        (new Subject())
            ->register(new UpdateUserInfo($owner, (int) $installation_id, (int) $rid, $repo_full_name, $default_branch, null, $git_type))
            ->handle();

        if ('branch' === $ref_type) {
            \App\Build::deleteByBranch($ref, (int) $rid, $git_type);
        }
    }
}
