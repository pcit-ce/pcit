<?php

declare(strict_types=1);

namespace PCIT\Runner\Agent\kube;

use PCIT\Runner\Agent\Interfaces\RunnerHandlerInterface;

class KubeHandler implements RunnerHandlerInterface
{
    public function handle(int $job_id): void
    {
    }

    public function runStep(int $job_id, string $container_config, ?string $step = null): void
    {
    }
}
