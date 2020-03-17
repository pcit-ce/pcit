<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Events;

use PCIT\Runner\Events\Handler\CommandHandler;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class CommandHandlerTest extends TestCase
{
    public function test_parse(): void
    {
        $result = CommandHandler::parse('sh', 'step', 'image', ['echo 1', 'echo 2']);

        // var_dump($result);

        $this->assertEquals($result, 'CmVjaG87ZWNobwoKZWNobyAiPT0+IiBQaXBlbGluZSBbc3RlcF0gUnVuIE9uICI9PiIgW2ltYWdlXQoKc2xlZXAgMC4xO2VjaG87ZWNobwoKc2V0IC14CgplY2hvIDEKCmVjaG8gMgoK');
    }

    public function test_parse_inline_sh(): void
    {
        $yaml = <<<EOF
alpine:
  run: |
    echo 1
    echo 2
EOF;

        $commands = Yaml::parse($yaml)['alpine']['run'];

        $result = CommandHandler::parse('sh', 'step', 'image', [$commands]);

        // var_dump($result);

        $this->assertEquals($result, 'CmVjaG87ZWNobwoKZWNobyAiPT0+IiBQaXBlbGluZSBbc3RlcF0gUnVuIE9uICI9PiIgW2ltYWdlXQoKc2xlZXAgMC4xO2VjaG87ZWNobwoKc2V0IC14CgplY2hvIDEKZWNobyAyCgo=');
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

        $result = CommandHandler::parse('python', 'step', 'image', [$commands]);

        // var_dump($result);

        $this->assertEquals($result, 'aW1wb3J0IG9zCnByaW50KG9zLmVudmlyb25bJ1BBVEgnXSk=');
    }

    /**
     * @throws \Exception
     */
    public function test_command(): void
    {
        $command = CommandHandler::parse('sh', 'step', 'image', [
          'pwd',
          'composer install',
          'vendor/bin/phpunit',
      ]);

        $this->assertEquals(176, \strlen($command));
    }
}
