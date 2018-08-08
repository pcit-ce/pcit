<?php

declare(strict_types=1);

namespace KhsCI\Support\Webhooks\GitHub;

use KhsCI\Support\Webhooks\GitHub\UserBasicInfo\Account;

class Create
{
    /**
     * @param $json_content
     *
     * @return array
     */
    public static function handle($json_content)
    {
        $obj = json_decode($json_content);

        $rid = $obj->repository->id;

        $ref_type = $obj->ref_type;

        $installation_id = $obj->installation->id ?? null;

        $repository = $obj->repository;

        $org = ($obj->organization ?? false) ? true : false;

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        return [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'ref_type' => $ref_type,
            'account' => (new Account($repository_owner, $org)),
        ];
    }
}
