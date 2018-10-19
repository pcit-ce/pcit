<?php

declare(strict_types=1);

namespace PCIT\Tests\Support;

use PCIT\Support\DB;
use PCIT\Tests\PCITTestCase;

class DBTest extends PCITTestCase
{
    public function test(): void
    {
        DB::close();

        $result = DB::statement('select database()');

        $this->assertEquals(0, $result);
    }
}
