<?php

declare(strict_types=1);

namespace KhsCI\Support\Webhooks\GitHub;

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

        return [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'ref_type' => $ref_type,
        ];
    }
}
