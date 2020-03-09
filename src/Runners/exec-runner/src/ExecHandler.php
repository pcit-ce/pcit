<?php

declare(strict_types=1);

namespace PCIT\Runner\Agent\Exec;

use PCIT\Exception\PCITException;
use PCIT\Runner\Agent\Interfaces\RunnerHandlerInterface;
use PCIT\Support\CI;

class ExecHandler implements RunnerHandlerInterface
{
    public function handle(int $job_id): void
    {
        // update start time

        // drop prev log

        // git clone

        // download cache

        // service

        // handleSteps

        // upload cache (if success)

        // cleanup

        // if success
        throw new PCITException(CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS);
    }

    public function runStep(int $job_id, string $container_config, ?string $step = null): void
    {
    }
}
