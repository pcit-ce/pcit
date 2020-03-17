<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Events;

use JsonSchema\Constraints\BaseConstraint;
use PCIT\Runner\Events\Matrix;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class MatrixTest extends TestCase
{
    public function test_parseMatrix(): void
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

        $result1 = (array) BaseConstraint::arrayToObjectRecursive(Yaml::parse($yaml)['matrix']);
        $result2 = (array) BaseConstraint::arrayToObjectRecursive(Yaml::parse($yaml2)['matrix']);

        $result1 = Matrix::parseMatrix($result1);
        $result2 = Matrix::parseMatrix($result2);

        $this->assertEquals($result1, $result2);
    }
}
