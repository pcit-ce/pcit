<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

class Create
{
    /**
     * Create "repository", "branch", or "tag".
     *
     * @param $json_content
     *
     * @throws \Exception
     */
    public static function handle($json_content): void
    {
        [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'ref_type' => $ref_type,
            'account' => $account,
        ] = \PCIT\GitHub\Webhooks\Parser\Create::handle($json_content);

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name))
            ->handle();

        if ('branch' === $ref_type) {
            $branch = $ref_type;
        } elseif ('repository' === $ref_type) {
            $repository = $ref_type;
        } elseif ('tag' === $ref_type) {
            $tag = $ref_type;
        }
    }
}
