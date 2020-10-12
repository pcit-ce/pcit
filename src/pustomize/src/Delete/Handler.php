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
        $repository = $context->repository;

        $installation_id = $context->installation->id;
        $rid = $repository->id;
        $repo_full_name = $repository->full_name;
        $ref_type = $context->ref_type;
        $owner = $context->owner;
        $ref = $context->ref;
        $default_branch = $repository->default_branch;
        $git_type = $context->git_type;

        (new Subject())
            ->register(
                new UpdateUserInfo(
                    $owner,
                    (int) $installation_id,
                    (int) $rid,
                    $repo_full_name,
                    $default_branch,
                    null,
                    $repository->private ?? false,
                    $git_type
                )
            )
            ->handle();

        if ('branch' === $ref_type) {
            \App\Build::deleteByBranch($ref, (int) $rid, $git_type);
        }
    }
}
