<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Conditional;

use PCIT\Runner\Conditional\Branch;
use Tests\TestCase;

class BranchTest extends TestCase
{
    public function test(): void
    {
        $result = (new Branch('master', 'master'))->handle(true);
        $this->assertTrue($result);

        $result = (new Branch('refs/tags/1.0.*', 'refs/tags/1.0.1'))->handle(true);
        $this->assertTrue($result);

        $result = (new Branch('dev', 'master'))->handle(true);
        $this->assertFalse($result);
    }

    public function testInclude(): void
    {
        $obj = json_decode('{"include":"master"}');
        $result = (new Branch($obj, 'master'))->handle(true);
        $this->assertTrue($result);

        $obj = json_decode('{"include":"test/*"}');
        $result = (new Branch($obj, 'test/time'))->handle(true);
        $this->assertTrue($result);

        $obj = json_decode('{"include":"master"}');
        $result = (new Branch($obj, 'dev'))->handle(true);
        $this->assertFalse($result);
    }

    public function testExclude(): void
    {
        $obj = json_decode('{"exclude":"test/*"}');
        $result = (new Branch($obj, 'dev'))->handle(true);

        $this->assertTrue($result);

        $obj = json_decode('{"exclude":"master"}');
        $result = (new Branch($obj, 'master'))->handle(true);

        $this->assertFalse($result);
    }

    public function testArray(): void
    {
        $obj = json_decode('{"exclude":["dev"]}');
        $result = (new Branch($obj, 'master'))->handle(true);
        $this->assertTrue($result);

        $obj = json_decode('{"exclude":["dev","master"]}');
        $result = (new Branch($obj, 'master'))->handle(true);
        $this->assertFalse($result);

        $obj = json_decode('{"include":["master","dev"]');
        $result = (new Branch($obj, 'master'))->handle(true);
        $this->assertTrue($result);

        $obj = json_decode('{"include":["test/*","dev"]');
        $result = (new Branch($obj, 'test/time'))->handle(true);
        $this->assertTrue($result);
    }
}
