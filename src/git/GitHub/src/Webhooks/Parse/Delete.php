<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parse;

use PCIT\Framework\Support\Log;
use PCIT\GitHub\Webhooks\Parse\UserBasicInfo\Account;

class Delete
{
    /**
     * @param $json_content
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function handle($json_content)
    {
        Log::debug(null, null, 'Receive event', ['delete'], Log::INFO);

        $obj = json_decode($json_content);

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

        return compact('installation_id', 'rid', 'repo_full_name',
            'account', 'ref_type', 'ref');
    }
}
