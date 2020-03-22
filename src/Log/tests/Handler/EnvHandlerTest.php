<?php

declare(strict_types=1);

namespace PCIT\Log\Tests\Handler;

use PCIT\Log\Handler\EnvHandler;
use Tests\TestCase;

class EnvHandlerTest extends TestCase
{
    public function test(): void
    {
        $log = <<<EOF
2020-03-20T12:44:04.991615500Z log content
2020-03-20T12:44:04.991615500Z ::set-env name=action_state::yellow
2020-03-20T12:44:04.991615500Z log content
EOF;
        [$log,$env] = (new EnvHandler())->handle($log, 31);

        $this->assertEquals("2020-03-20T12:44:04.991615500Z log content\n2020-03-20T12:44:04.991615500Z log content", $log);

        $this->assertEquals(['action_state' => 'yellow'], $env);
    }
}
