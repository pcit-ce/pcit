<?php

declare(strict_types=1);

namespace PCIT\Builder\Tests\Events;

use PCIT\Builder\Events\Cache;
use PCIT\Support\CacheKey;
use PCIT\Support\DB;
use Symfony\Component\Yaml\Yaml;
use Tests\PCITTestCase;

class CacheTest extends PCITTestCase
{
    public $yaml;

    public $cache;

    /**
     * @throws \Exception
     */
    public function common(): void
    {
        $result = Yaml::parse($this->yaml);

        $json = json_encode($result);

        $stub = $this->createMock(Cache::class);

        $stub->method('getPrefix')->willReturn('gittype_rid_branch');

        $cache = new Cache(1,
            1, '', 'github', 1,
            'master', json_decode($json)->cache);

        $cache->handle();

        $this->cache = \PCIT\Support\Cache::store()->get(CacheKey::cacheKey(1));
    }

    /**
     * @throws \Exception
     */
    public function test_array(): void
    {
        DB::close();

        $yaml = <<<'EOF'
cache:
  - dir
EOF;

        $this->yaml = $yaml;

        $this->common();

        $this->assertEquals('PCIT_S3_CACHE=["dir"]', json_decode($this->cache)->Env[6]);
    }

    /**
     * @throws \Exception
     */
    public function test_string(): void
    {
        DB::close();

        $yaml = <<<'EOF'
cache: dir
EOF;

        $this->yaml = $yaml;

        $this->common();

        $this->assertEquals('PCIT_S3_CACHE=["dir"]', json_decode($this->cache)->Env[6]);
    }
}
