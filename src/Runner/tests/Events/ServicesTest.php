<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Events;

use JsonSchema\Constraints\BaseConstraint;
use PCIT\Runner\Client;
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
    command: cmd
EOF;
        $services = BaseConstraint::arrayToObjectRecursive(Yaml::parse($yaml)['services']);

        (new Services($services, 1, new Client(), []))->handle();

        $result = \Cache::store()->hget(CacheKey::serviceHashKey(1), 'mysql');

        // var_dump($result);

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

        (new Services($services, 1, new Client(), []))->handle();

        $result = \Cache::store()->hget(CacheKey::serviceHashKey(1), 'mysql');

        $this->assertNull(json_decode($result)->Entrypoint);
    }

    public function test_value_with_var(): void
    {
        $yaml = <<<EOF
services:
  mysql:
    image: \${K}
    env:
    - K10=\${K1}
    - k20=\${K2}
EOF;
        $services = BaseConstraint::arrayToObjectRecursive(Yaml::parse($yaml)['services']);

        $client = new Client();
        $client->system_env = ['K=V'];
        $client->system_job_env = ['K1=v1'];

        (new Services($services, 1, $client, ['K2' => 'v2']))->handle();

        $result = \Cache::store()->hget(CacheKey::serviceHashKey(1), 'mysql');

        // var_dump($result);

        $result = json_decode($result);
        $this->assertNull($result->Entrypoint);
        $this->assertEquals('V', $result->Image);
        $this->assertEquals('K10=v1', $result->Env[3]);
        $this->assertEquals('k20=v2', $result->Env[4]);
    }
}
