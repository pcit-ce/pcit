<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Events;

use PCIT\Runner\Events\ActionHandler;
use PCIT\Runner\Events\Pipeline;
use PCIT\Runner\JobGenerator;
use PHPUnit\Framework\TestCase;

class ActionHandlerTest extends TestCase
{
    public function test_actionsHandler(): void
    {
        $pipeline = null;
        $jobGenerator = new JobGenerator();
        $jobGenerator->job_id = 1;
        $step = new Pipeline($pipeline, null, $jobGenerator, null);
        $actionHandler = new ActionHandler($step);
        $result = $actionHandler->handle(
            'actions',
            'github://actions/checkout@main'
        );

        $this->assertEquals(
            'node /var/run/actions/actions/checkout/dist/index.js',
            $result[0]
        );
    }

    public function test_actionsHandler_with_path(): void
    {
        $pipeline = null;
        $jobGenerator = new JobGenerator();
        $jobGenerator->job_id = 1;
        $step = new Pipeline($pipeline, null, $jobGenerator, null);
        $actionHandler = new ActionHandler($step);

        $result = $actionHandler->handle(
            'actions',
            'github://khs1994-docker/lnmp/.github/actions/setup-php'
        );

        $this->assertEquals(
            'node /var/run/actions/khs1994-docker/lnmp/.github/actions/setup-php/lib/setup-php.js',
            $result[0]
        );
    }
}
