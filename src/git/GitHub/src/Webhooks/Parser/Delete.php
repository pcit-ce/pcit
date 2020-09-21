<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\DeleteContext;

class Delete
{
    public static function handle(string $webhooks_content): DeleteContext
    {
        \Log::info('Receive event', ['type' => 'delete']);

        $context = new DeleteContext([], $webhooks_content);
        $repository = $context->repository;

        $context->rid = $repository->id;
        $context->repo_full_name = $repository->full_name;

        return $context;
    }
}
