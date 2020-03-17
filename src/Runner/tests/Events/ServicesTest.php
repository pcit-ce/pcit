<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Events;

use JsonSchema\Constraints\BaseConstraint;
use PCIT\Runner\Events\Services;
use PCIT\Support\CacheKey;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class ServicesTest extends TestCase
{
    public function test(): void
    {
        $yaml = <<<EOF
services:
  mysql:
    image: mysql
    entrypoint:
    - /path
    - cmd
    commands:
    - cmd
EOF;
        $services = BaseConstraint::arrayToObjectRecursive(Yaml::parse($yaml)['services']);

        (new Services($services, 1, []))->handle();

        $result = \Cache::store()->hget(CacheKey::serviceHashKey(1), 'mysql');

        $this->assertEquals(json_decode($result)->Entrypoint, [
            '/path', 'cmd',
        ]);

        $this->assertEquals(json_decode($result)->Cmd, [
            'cmd',
        ]);
    }

    public function testNull(): void
    {
        $yaml = <<<EOF
services:
  mysql:
EOF;
        $services = BaseConstraint::arrayToObjectRecursive(Yaml::parse($yaml)['services']);

        (new Services($services, 1, []))->handle();

        $result = \Cache::store()->hget(CacheKey::serviceHashKey(1), 'mysql');

        $this->assertNull(json_decode($result)->Entrypoint);
    }
}
