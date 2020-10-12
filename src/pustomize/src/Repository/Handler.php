<?php

declare(strict_types=1);

namespace PCIT\Pustomize\Repository;

use App\Repo;
use PCIT\GPI\Webhooks\Context\RepositoryContext;

class Handler
{
    public function handle(RepositoryContext $context): void
    {
        $repository = $context->repository;

        if (\in_array($context->action, ['privatized', 'publicized'])) {
            Repo::updateRepoInfo(
                $repository->id,
                $repository->full_name,
                null,
                null,
                $repository->default_branch,
                $repository->private,
                $context->git_type,
            );
        }
    }
}
