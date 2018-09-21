<?php

declare(strict_types=1);

namespace PCIT\Tests\Service\Build\Conditional;

use KhsCI\Service\Build\Conditional\Branch;
use PCIT\Tests\PCITTestCase;

class BranchTest extends PCITTestCase
{
    /**
     * @throws \Exception
     */
    public function test(): void
    {
        $result = (new Branch('master', 'master'))->regHandle();
        $this->assertTrue($result);

        $result = (new Branch('dev', 'master'))->regHandle();
        $this->assertFalse($result);
    }

    /**
     * @throws \Exception
     */
    public function testObj(): void
    {
        $obj = json_decode('{"include":"master"}');
        $result = (new Branch($obj, 'master'))->regHandle();
        $this->assertTrue($result);

        $obj = json_decode('{"exclude":"master"}');
        $result = (new Branch($obj, 'master'))->regHandle();
        $this->assertFalse($result);

        $obj = json_decode('{"exclude":"master","include":"master"}');
        $result = (new Branch($obj, 'master'))->regHandle();
        $this->assertFalse($result);
    }
}
