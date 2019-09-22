<?php

declare(strict_types=1);

namespace PCIT\Plugins\Tests\email;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    protected function setUp(): void
    {
        (Dotenv::create(__DIR__))->load();
    }

    /**
     * @group dont-test
     */
    public function testSend(): void
    {
        require __DIR__.'/../../../plugins/notification/email/index.php';
    }
}
