<?php

declare(strict_types=1);

namespace PCIT\Pustomize\CheckRun;

use App\Events\GetBuild;
use App\Job;
use PCIT\GPI\Webhooks\Context\CheckRunContext;
use PCIT\Runner\JobGenerator;

class Handler
{
    public function handle(CheckRunContext $context): void
    {
        if (!\in_array($context->action, ['rerequested'], true)) {
            return;
        }

        $action = $context->action;
        $external_id = $context->external_id;

        // 用户点击了某一 run 的 Re-run
        if ('rerequested' === $action) {
            $build_id = Job::getBuildKeyId((int) $external_id);

            (new JobGenerator())->handle((new GetBuild())->handle($build_id), (int) $external_id);

            return;
        }

        // 用户点击了按钮，CI 推送修复补丁
        // 'requested_action' === $action &&
    }
}
