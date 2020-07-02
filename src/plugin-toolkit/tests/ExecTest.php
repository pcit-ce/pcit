<?php

declare(strict_types=1);

namespace PCIT\Plugin\Toolkit\Tests;

use PCIT\Plugin\Toolkit\Exec;
use Tests\TestCase;

class ExecTest extends TestCase
{
    public function test(): void
    {
        (new Exec())->exec('ls');

        $this->expectOutputString('[36m[command]'.'ls'.'[0m'.PHP_EOL);
    }
}
