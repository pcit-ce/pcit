<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Events;

use PCIT\Runner\Events\Handler\ShellHandler;
use Tests\TestCase;

class ShellHandlerTest extends TestCase
{
    public function test_error_shell(): void
    {
        $result = (new ShellHandler())->handle('error', ['echo', '1']);

        $this->assertEquals([null, null], $result);
    }

    public function test(): void
    {
        $result = (new ShellHandler())->handle('sh', ['echo', '1']);

        // var_dump($result);

        $this->assertEquals([['/bin/sh', '-c'], [
            'echo $CI_SCRIPT | base64 -d | timeout 21600 sh -e',
        ]], $result);
    }

    public function test_null_commands(): void
    {
        $result = (new ShellHandler())->handle('sh', []);

        // var_dump($result);

        $this->assertEquals([null, null], $result);
    }
}
