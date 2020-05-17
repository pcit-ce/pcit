<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\CreateContext;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;

class Create
{
    public static function handle(string $webhooks_content): CreateContext
    {
        $obj = json_decode($webhooks_content);

        $repository = $obj->repository;
        $rid = $obj->repository->id;
        $repo_full_name = $repository->full_name;

        $ref_type = $obj->ref_type;

        $installation_id = $obj->installation->id ?? null;

        $org = ($obj->organization ?? false) ? true : false;

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        $context = new CreateContext([], $webhooks_content);

        $context->installation_id = $installation_id;
        $context->rid = $rid;
        $context->repo_full_name = $repo_full_name;
        $context->ref_type = $ref_type;
        $context->account = new Account($repository_owner, $org);

        return $context;
    }
}
