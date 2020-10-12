<?php

declare(strict_types=1);

namespace PCIT\Pustomize\Create;

use PCIT\GPI\Webhooks\Context\CreateContext;
use PCIT\Subject;
use PCIT\UpdateUserInfo;

class Handler
{
    public function handle(CreateContext $context): void
    {
        $installation_id = $context->installation->id;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $ref_type = $context->ref_type;
        $owner = $context->owner;
        $default_branch = $context->repository->default_branch;
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
                $context->repository->private ?? false,
                $git_type
            )
            )
            ->handle();

        if ('branch' === $ref_type) {
            $branch = $ref_type;
        } elseif ('repository' === $ref_type) {
            $repository = $ref_type;
        } elseif ('tag' === $ref_type) {
            $tag = $ref_type;
        }
    }
}
