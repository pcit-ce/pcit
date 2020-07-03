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

    public function test_with_spec(): void
    {
        $log = <<<EOF
    2020-03-20T12:44:04.991615500Z log content
    2020-03-20T12:44:04.991615500Z ::add-mask::%0A%0DMona The Octocat %25 %0D %0A %0A : ,%0A
    2020-03-20T12:44:04.991615500Z log content
    2020-03-20T12:44:04.991615500Z \n\rMona The Octocat % \r \n \n
    2020-07-02T17:14:39.652670900Z  : ,\n
    EOF;
        [$log,$hide_value] = (new MaskHandler())->handle($log, 31);

        // $this->assertEquals("2020-03-20T12:44:04.991615500Z log content\n2020-03-20T12:44:04.991615500Z log content\n2020-03-20T12:44:04.991615500Z ***", $log);

        $this->assertEquals(["\n\rMona The Octocat % \r \n \n : ,\n"], $hide_value);
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
