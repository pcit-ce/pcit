<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\Components\Repository;
use PCIT\GPI\Webhooks\Context\Components\User\Owner;
use PCIT\GPI\Webhooks\Context\CreateContext;

class Create
{
    public static function handle(string $webhooks_content): CreateContext
    {
        $obj = json_decode($webhooks_content);

        $repository = new Repository($obj->repository);
        $org = ($obj->organization ?? false) ? true : false;
        $repository->owner = new Owner($repository->owner, $org);

        $ref_type = $obj->ref_type;

        $installation_id = $obj->installation->id ?? null;

        $context = new CreateContext([], $webhooks_content);

        $context->installation_id = $installation_id;
        $context->rid = $repository->id;
        $context->repo_full_name = $repository->full_name;
        $context->ref_type = $ref_type;
        $context->owner = $repository->owner;

        return $context;
    }
}
