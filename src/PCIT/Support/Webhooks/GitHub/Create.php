<?php

declare(strict_types=1);

namespace PCIT\Support\Webhooks\GitHub;

use PCIT\Support\Webhooks\GitHub\UserBasicInfo\Account;

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

        $repository = $obj->repository;
        $rid = $obj->repository->id;
        $repo_full_name = $repository->full_name;

        $ref_type = $obj->ref_type;

        $installation_id = $obj->installation->id ?? null;

        $org = ($obj->organization ?? false) ? true : false;

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        return [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'ref_type' => $ref_type,
            'account' => (new Account($repository_owner, $org)),
        ];
    }
}
