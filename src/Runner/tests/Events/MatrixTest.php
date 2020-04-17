<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Events;

use JsonSchema\Constraints\BaseConstraint;
use PCIT\Runner\Events\Matrix;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class MatrixTest extends TestCase
{
    public function parse(string $yaml): array
    {
        $result = (array) BaseConstraint::arrayToObjectRecursive(Yaml::parse($yaml)['matrix']);

        return Matrix::handle($result);
    }

    public function test(): void
    {
        $yaml = <<<EOF
matrix:
  PHP_VERSION:
    - 7.4.2
    - 7.1.27
  REDIS_VERSION:
    - 5.0.7
  MYSQL_VERSION:
    - 8.0.19
EOF;

        $yaml2 = <<<EOF
matrix:
  include:
    - PHP_VERSION: 7.4.2
      MYSQL_VERSION: 8.0.19
      REDIS_VERSION: 5.0.7
    - PHP_VERSION: 7.1.27
      REDIS_VERSION: 5.0.7
      MYSQL_VERSION: 8.0.19
EOF;

        $result1 = $this->parse($yaml);
        $result2 = $this->parse($yaml2);

        $this->assertEquals($result2, $result1);

        $yaml3 = <<<EOF
matrix:
  PHP_VERSION:
  - 7.4.2
  REDIS_VERSION:
  - 5.0.7
  MYSQL_VERSION:
  - 8.0.19
EOF;
        $result3 = $this->parse($yaml3);

        // var_dump($result3);

        $yaml4 = <<<EOF
matrix:
  include:
  - PHP_VERSION: 7.4.2
    MYSQL_VERSION: 8.0.19
    REDIS_VERSION: 5.0.7
EOF;
        $result4 = $this->parse($yaml4);

        // var_dump($result4);

        $this->assertEquals($result4, $result3);
    }

    public function testMatrixWithinclude(): void
    {
        $yaml5 = <<<EOF
matrix:
  PHP_VERSION:
  - 7.4.2
  include:
  - PHP_VERSION: nightly
    MYSQL_VERSION: 8.0.19
    REDIS_VERSION: 5.0.7
EOF;
        $result5 = $this->parse($yaml5);

        $yaml6 = <<<EOF
matrix:
  include:
  - PHP_VERSION: 7.4.2
  - PHP_VERSION: nightly
    MYSQL_VERSION: 8.0.19
    REDIS_VERSION: 5.0.7
EOF;
        $result6 = $this->parse($yaml6);

        $this->assertEquals($result6, $result5);
    }

    public function testMatrixWithExclude(): void
    {
        $yaml7 = <<<EOF
matrix:
  PHP_VERSION:
  - 7.4.2
  - 7.2.0
  NGINX_VERSION:
  - 1.17.10
  include:
  - PHP_VERSION: nightly
    MYSQL_VERSION: 8.0.19
    REDIS_VERSION: 5.0.7
  exclude:
  - PHP_VERSION: 7.2.0
    NGINX_VERSION: 1.17.10
EOF;
        $result7 = $this->parse($yaml7);

        $yaml8 = <<<EOF
matrix:
  include:
  - PHP_VERSION: 7.4.2
    NGINX_VERSION: 1.17.10
  - PHP_VERSION: nightly
    MYSQL_VERSION: 8.0.19
    REDIS_VERSION: 5.0.7
EOF;
        $result8 = $this->parse($yaml8);

        $this->assertEquals($result8, $result7);
    }
}
