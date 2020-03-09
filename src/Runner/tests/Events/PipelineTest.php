<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Events;

use PCIT\Runner\Client;
use PCIT\Runner\Events\Pipeline;
use PHPUnit\Framework\TestCase;

class PipelineTest extends TestCase
{
    public function test_actionsHandler(): void
    {
        $pipeline = null;
        $client = new Client();
        $client->job_id = 1;
        $result = (new Pipeline($pipeline, null, $client, null, null))->actionsHandler(
            'actions', 'github://actions/checkout@master'
        );

        $this->assertEquals('node /var/run/actions/actions/checkout/dist/index.js',
        $result[0]);
    }

    public function test_actionsHandler_with_path(): void
    {
        $pipeline = null;
        $client = new Client();
        $client->job_id = 1;
        $result = (new Pipeline($pipeline, null, $client, null))->actionsHandler(
            'actions', 'github://khs1994-docker/lnmp/.github/actions/setup-php'
        );

        $this->assertEquals('node /var/run/actions/khs1994-docker/lnmp/.github/actions/setup-php/lib/setup-php.js',
        $result[0]);
    }
}
