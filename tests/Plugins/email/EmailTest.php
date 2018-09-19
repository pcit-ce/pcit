<?php

declare(strict_types=1);

namespace KhsCI\Tests\Plugins\email;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    protected function setUp(): void
    {
        (new Dotenv(__DIR__))->load();
    }

    /**
     * @group dont-test
     */
    public function testSend(): void
    {
        require __DIR__.'/../../../plugins/email/index.php';
    }
}
