<?php

declare(strict_types=1);

namespace PCIT\Builder\Tests\Conditional;

use PCIT\Builder\Conditional\Branch;
use Tests\TestCase;

class BranchTest extends TestCase
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
    public function testInclude(): void
    {
        $obj = json_decode('{"include":"master"}');
        $result = (new Branch($obj, 'master'))->regHandle();
        $this->assertTrue($result);

        $obj = json_decode('{"include":"master"}');
        $result = (new Branch($obj, 'dev'))->regHandle();
        $this->assertFalse($result);
    }

    public function testExclude(): void
    {
        $obj = json_decode('{"exclude":"master"}');
        $result = (new Branch($obj, 'dev'))->regHandle();

        $this->assertTrue($result);

        $obj = json_decode('{"exclude":"master"}');
        $result = (new Branch($obj, 'master'))->regHandle();

        $this->assertFalse($result);
    }

    public function testBoth(): void
    {
        $obj = json_decode('{"exclude":"master","include":"master"}');
        $result = (new Branch($obj, 'master'))->regHandle();
        $this->assertFalse($result);
    }

    public function testObj(): void
    {
        $obj = json_decode('{"exclude":["dev"],"include":["master"]}');
        $result = (new Branch($obj, 'master'))->regHandle();
        $this->assertTrue($result);

        $obj = json_decode('{"exclude":["dev","master"],"include":["master"]}');
        $result = (new Branch($obj, 'master'))->regHandle();
        $this->assertFalse($result);

        $obj = json_decode('{"include":["master","dev"],"exclude":["test*"]}');
        $result = (new Branch($obj, 'master'))->regHandle();
        $this->assertTrue($result);
    }
}
