<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Abstracts\CheckSuiteAbstract;

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

        $this->pustomize($context, 'github');
    }
}
