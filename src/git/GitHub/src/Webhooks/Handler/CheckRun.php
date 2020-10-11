<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Abstracts\CheckRunAbstract;

class CheckRun extends CheckRunAbstract
{
    public $git_type = 'github';

    /**
     * created updated rerequested requested_action.
     *
     * rerequested 用户点击了重新运行(Re-run)按钮
     */
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\CheckRun::handle($webhooks_content);

        $this->pustomize($context, 'github');
    }
}
