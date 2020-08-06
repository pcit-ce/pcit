<?php

declare(strict_types=1);

namespace PCIT\Log\Tests\Handler;

use PCIT\Log\Handler\DebugHandler;
use Tests\TestCase;

class DebugHandlerTest extends TestCase
{
    public function test(): void
    {
        $log = <<<EOF
2020-03-20T12:44:04.991615500Z log content
2020-03-20T12:44:04.991615500Z ::debug::{message}
2020-03-20T12:44:04.991615500Z ::debug file=app.js,line=1::Entered octocatAddition method
2020-03-20T12:44:04.991615500Z log content
EOF;

        [$log,$context] = (new DebugHandler())->handle($log, 31);

        // var_dump($log);
        // var_dump($context);

        $this->assertTrue(true);

        $this->assertEquals([
            [
                'file' => 'app.js',
                'line' => 1,
            ],
        ], $context);
    }
}
