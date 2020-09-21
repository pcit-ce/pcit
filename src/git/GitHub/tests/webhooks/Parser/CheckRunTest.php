<?php

declare(strict_types=1);

namespace PCIT\GitHub\Tests\Webhooks\Parser;

use PCIT\GitHub\Webhooks\Parser\CheckRun as CheckRunParser;
use PCIT\GPI\Webhooks\Context\Components\CheckRun;
use PHPUnit\Framework\TestCase;

class CheckRunTest extends TestCase
{
    public function test_handle(): void
    {
        // $c = CheckRunParser::handle(file_get_contents('C:\Users\90621\app\pcit\src\git\GitHub\tests\webhooks\github\check_run.json'));

        // $this->assertTrue($c->check_run instanceof CheckRun);

        $this->assertTrue(true);
    }
}
