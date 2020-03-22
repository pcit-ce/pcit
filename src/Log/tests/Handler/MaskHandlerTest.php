<?php

declare(strict_types=1);

namespace PCIT\Log\Tests\Handler;

use PCIT\Log\Handler\MaskHandler;
use Tests\TestCase;

class MaskHandlerTest extends TestCase
{
    public function test(): void
    {
        $log = <<<EOF
    2020-03-20T12:44:04.991615500Z log content
    2020-03-20T12:44:04.991615500Z ::add-mask::Mona The Octocat
    2020-03-20T12:44:04.991615500Z log content
    EOF;
        [$log,$hide_value] = (new MaskHandler())->handle($log, 31);

        $this->assertEquals("2020-03-20T12:44:04.991615500Z log content\n2020-03-20T12:44:04.991615500Z log content", $log);

        $this->assertEquals(['Mona The Octocat'], $hide_value);
    }

    public function test_with_premask(): void
    {
        $log = <<<EOF
    2020-03-20T12:44:04.991615500Z log content
    2020-03-20T12:44:04.991615500Z ::add-mask::Mona The Octocat
    2020-03-20T12:44:04.991615500Z log content
    2020-03-20T12:44:04.991615500Z hide value
    EOF;
        [$log,$hide_value] = (new MaskHandler())->handle($log, 31, ['hide value']);

        // var_dump($log);

        $this->assertEquals("2020-03-20T12:44:04.991615500Z log content\n2020-03-20T12:44:04.991615500Z log content\n2020-03-20T12:44:04.991615500Z ***", $log);

        $this->assertEquals(['Mona The Octocat'], $hide_value);
    }
}
