<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\DeleteContext;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;

class Delete
{
    public static function handle(string $webhooks_content): DeleteContext
    {
        \Log::info('Receive event', ['type' => 'delete']);

        $obj = json_decode($webhooks_content);

        $ref_type = $obj->ref_type;
        $ref = $obj->ref;

        $repository = $obj->repository;
        $rid = $repository->id;
        $repo_full_name = $repository->full_name;

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        $installation_id = $obj->installation->id ?? null;

        $org = ($obj->organization ?? false) ? true : false;

        $account = (new Account($repository_owner, $org));

        $context = new DeleteContext([], $webhooks_content);

        $context->installation_id = $installation_id;
        $context->rid = $rid;
        $context->repo_full_name = $repo_full_name;
        $context->account = $account;
        $context->ref_type = $ref_type;
        $context->ref = $ref;

        return $context;
    }
}
