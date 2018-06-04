<?php

declare(strict_types=1);

namespace KhsCI\Tests\Plugins;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class TencentCosV5Test extends TestCase
{
    public function setUp(): void
    {
        (new Dotenv(__DIR__))->load();
    }

    /**
     * @group DON'TTEST
     */
    public function test(): void
    {
        $result = null;

        require __DIR__.'/../../../plugins/tencent_cos_v5/index.php';

        $this->assertEquals(0, $result['code']);
    }
}
