<?php

namespace KhsCI\Tests\Plugins\email;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    protected function setUp()
    {
        (new Dotenv(__DIR__))->load();
    }

    public function testSend()
    {
        require __DIR__."/../../../plugins/email/index.php";
    }
}
