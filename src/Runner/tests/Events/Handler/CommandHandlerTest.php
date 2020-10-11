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

        $this->assertEquals('ZWNobyAnCiMjW21ldGFkYXRhXQp7CiAgICAic3RlcCIgOiAic3RlcCIsCiAgICAiaW1hZ2UiOiAiaW1hZ2UiCn0KIyNbZW5kbWV0YWRhdGFdCicKY2F0ID4gL2Rldi9zdGRvdXQgPDwnRU9GJwobWzM2bVtjb21tYW5kXWVjaG8gMRtbMG0KRU9GCmVjaG8gMQoKY2F0ID4gL2Rldi9zdGRvdXQgPDwnRU9GJwobWzM2bVtjb21tYW5kXWVjaG8gMhtbMG0KRU9GCmVjaG8gMgoK', $result);
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

        $this->assertEquals('ZWNobyAnCiMjW21ldGFkYXRhXQp7CiAgICAic3RlcCIgOiAic3RlcCIsCiAgICAiaW1hZ2UiOiAiaW1hZ2UiCn0KIyNbZW5kbWV0YWRhdGFdCicKY2F0ID4gL2Rldi9zdGRvdXQgPDwnRU9GJwobWzM2bVtjb21tYW5kXWVjaG8gMRtbMG0KRU9GCmVjaG8gMQpjYXQgPiAvZGV2L3N0ZG91dCA8PCdFT0YnChtbMzZtW2NvbW1hbmRdZWNobyAyG1swbQpFT0YKZWNobyAyCgo=', $result);
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

        $this->assertEquals('aW1wb3J0IG9zCnByaW50KG9zLmVudmlyb25bJ1BBVEgnXSk=', $result);
    }

    public function test_command(): void
    {
        $command = CommandHandler::parse('sh', 'step', 'image', [
            'pwd',
            'composer install',
            'vendor/bin/phpunit',
        ]);

        $this->assertEquals(416, \strlen($command));
    }

    public function test_raw(): void
    {
        $yaml = <<<EOF
run: |
  echo 11 11 111

  echo 1\
  1 \
  11 \
  1\
  11

  echo "1\\1"
  echo 1\\\\1

  echo "\${a}"

  echo $\
  {\
  a}

  echo $\
  a

  echo \$a

  ls

  l\
  s

  co\
  mpo\
  ser \
  in\
  s\
  t\
  all \
  --a b\
   --c\
   d --\${a} \${BB}

  composer install --a b --c d --\${a} \${BB}

  /opt/pcit/toolkit/pcit-retry -t 2 --sleep 5 'echo "y u no work"; false'
EOF;

        $command = Yaml::parse($yaml)['run'];

        $result = CommandHandler::parse('sh', 'step', 'image', [$command], true);

        // var_dump($result);

        $this->assertTrue(true);
    }
}
