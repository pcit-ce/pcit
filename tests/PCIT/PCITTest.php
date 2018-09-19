<?php

declare(strict_types=1);

namespace PCIT\Tests;

use Exception;
use KhsCI\Support\Cache;
use KhsCI\Support\DB;

class PCITTest extends PCITTestCase
{
    /**
     * @return \KhsCI\KhsCI
     *
     * @throws Exception
     */
    public function example()
    {
        return $this->getTest();
    }

    /**
     * @throws Exception
     */
    public function testCache(): void
    {
        $redis = Cache::store();

        $output = $redis->set('k', 1);

        $this->assertEquals(1, $output);
    }

    /**
     * @throws Exception
     */
    public function testDB(): void
    {
        $output = DB::statement('select 1');

        $this->assertEquals(0, $output);
    }
}
