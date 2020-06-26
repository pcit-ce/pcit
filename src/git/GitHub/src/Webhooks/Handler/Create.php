<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Subject;
use PCIT\GPI\Webhooks\Handler\UpdateUserInfo;

class Create
{
    /**
     * Create "repository", "branch", or "tag".
     *
     * @throws \Exception
     */
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Create::handle($webhooks_content);
        $installation_id = $context->installation_id;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $ref_type = $context->ref_type;
        $owner = $context->owner;
        $default_branch = $context->repository->default_branch;

        (new Subject())
            ->register(new UpdateUserInfo($owner, (int) $installation_id, (int) $rid, $repo_full_name, $default_branch))
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
