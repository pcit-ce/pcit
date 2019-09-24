<?php

declare(strict_types=1);

namespace PCIT\Builder\Tests\Events;

use PCIT\Builder\Events\CommandHandler;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class CommandHandlerTest extends TestCase
{
    public function test_parse(): void
    {
        $result = CommandHandler::parse('sh', 'sh', 'image', ['echo 1', 'echo 2']);

        // var_dump($result);

        $this->assertEquals($result, 'CmVjaG87ZWNobwoKZWNobyAiPT0+IiBQaXBlbGluZSBzaCBSdW4gT24gIj0+IiBpbWFnZQoKc2xlZXAgMC4xO2VjaG87ZWNobwoKc2V0IC14CgplY2hvIDEKCmVjaG8gMgoK');
    }

    public function test_parse_not_sh(): void
    {
        $yaml = <<<EOF
python:
  run: |
    import os
    print(os.environ['PATH'])
EOF;

        $commands = Yaml::parse($yaml)['python']['run'];

        $result = CommandHandler::parse('python', 'python', 'image', [$commands]);

        // var_dump($result);

        $this->assertEquals($result, 'aW1wb3J0IG9zCnByaW50KG9zLmVudmlyb25bJ1BBVEgnXSk=');
    }

    /**
     * @throws \Exception
     */
    public function test_command(): void
    {
        $command = CommandHandler::parse('sh', 'mock', 'khs1994/mock', [
          'pwd',
          'composer install',
          'vendor/bin/phpunit',
      ]);

        $this->assertEquals(180, \strlen($command));
    }
}
