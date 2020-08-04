<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\Components\Repository;
use PCIT\GPI\Webhooks\Context\Components\User\Owner;
use PCIT\GPI\Webhooks\Context\DeleteContext;

class Delete
{
    public static function handle(string $webhooks_content): DeleteContext
    {
        \Log::info('Receive event', ['type' => 'delete']);

        $obj = json_decode($webhooks_content);

        $ref_type = $obj->ref_type;
        $ref = $obj->ref;

        $repository = new Repository($obj->repository);
        $org = ($obj->organization ?? false) ? true : false;
        $repository->owner = new Owner($repository->owner, $org);

        $installation_id = $obj->installation->id ?? null;

        $context = new DeleteContext([], $webhooks_content);

        $context->installation_id = $installation_id;
        $context->rid = $repository->id;
        $context->repo_full_name = $repository->full_name;
        $context->owner = $repository->owner;
        $context->ref_type = $ref_type;
        $context->ref = $ref;

        return $context;
    }
}
