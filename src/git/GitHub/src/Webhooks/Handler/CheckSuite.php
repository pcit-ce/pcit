<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Abstracts\CheckSuiteAbstract;
use PCIT\GPI\Webhooks\Handler\Subject;
use PCIT\GPI\Webhooks\Handler\UpdateUserInfo;

class CheckSuite extends CheckSuiteAbstract
{
    public $git_type = 'github';

    /**
     * completed.
     *
     * requested: when new code is pushed to the app's repository
     *
     * rerequested: re-run the entire check suite
     *
     * @throws \Exception
     */
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\CheckSuite::handle($webhooks_content);

        if (!\in_array($context->action, ['requested'])) {
            return;
        }

        $installation_id = $context->installation_id;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $branch = $context->branch;
        $commit_id = $context->commit_id;
        $action = $context->action;
        $owner = $context->owner;
        $check_suite_id = $context->check_suite_id;
        $default_branch = $context->repository->default_branch;

        (new Subject())
            ->register(new UpdateUserInfo($owner, (int) $installation_id, (int) $rid, $repo_full_name, $default_branch))
            ->handle();

        if ('requested' === $action) {
            if ($context->check_suite->pull_requests) {
            } else {
                $this->handlePush($context, 'github');
            }

            return;
        }

        // 'rerequested' === $action && Build::updateBuildStatusByCommitId('pending', (int) $rid, $branch, $commit_id);
    }
}
