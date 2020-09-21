<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\CreateContext;

class Create
{
    public static function handle(string $webhooks_content): CreateContext
    {
        $context = new CreateContext([], $webhooks_content);
        $repository = $context->repository;

        $context->rid = $repository->id;
        $context->repo_full_name = $repository->full_name;

        return $context;
    }
}
