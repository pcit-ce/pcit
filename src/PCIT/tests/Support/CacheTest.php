<?php

declare(strict_types=1);

namespace PCIT\Tests\Support;

use PCIT\Support\Cache;
use Tests\PCITTestCase;

class CacheTest extends PCITTestCase
{
    public function test(): void
    {
        Cache::store()->set('key', 'value');

        $this->assertEquals('value', Cache::store()->get('key'));
    }
}
