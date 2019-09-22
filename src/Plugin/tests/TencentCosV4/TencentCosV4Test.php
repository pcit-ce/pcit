<?php

declare(strict_types=1);

namespace PCIT\Plugins\Tests\TencentCosV4;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class TencentCosV4Test extends TestCase
{
    protected function setUp(): void
    {
        (Dotenv::create(__DIR__))->load();
    }

    /**
     * @group dont-test
     */
    public function test(): void
    {
        $ret = null;

        require __DIR__.'/../../../plugins/storage/tencent_cos_v4/index.php';

        $this->assertEquals(0, $ret['code']);
    }
}
