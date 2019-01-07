<?php

declare(strict_types=1);

namespace PCIT\Tests\Service\Build\Events;

use PCIT\Service\Build\Events\Services;
use PCIT\Support\Cache;
use PCIT\Support\CacheKey;
use PCIT\Support\DB;
use PCIT\Tests\PCITTestCase;
use Symfony\Component\Yaml\Yaml;

class ServicesTest extends PCItTestCase
{
    public function testHandle(): void
    {
        $yaml = <<<EOF

services:
  mysql:
  redis:

EOF;
        $services = Yaml::parse($yaml)['services'];
        DB::close();
        (new Services($services, 1, null))->handle();
        $result = Cache::store()->hget(CacheKey::serviceHashKey(1), 'mysql');

        // var_dump($result);

        $this->assertJson($result);
    }
}
