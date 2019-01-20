<?php

declare(strict_types=1);

namespace PCIT\Builder\Tests\Events;

use PCIT\Builder\Events\Matrix;
use PCIT\Tests\PCITTestCase;
use Symfony\Component\Yaml\Yaml;

class MatrixTest extends PCITTestCase
{
    public function test_parseMatrix(): void
    {
        $yaml = <<<EOF
matrix:
  PHP_VERSION:
    - 7.2.13
    - 7.1.23
  REDIS_VERSION:
    - 5.0.0

EOF;

        $yaml2 = <<<EOF
matrix:
  include:
    - PHP_VERSION: 7.2.13
      REDIS_VERSION: 5.0.0
    - PHP_VERSION: 7.1.23
      REDIS_VERSION: 5.0.0
EOF;

        $result1 = Yaml::parse($yaml);
        $result2 = Yaml::parse($yaml2);

        $result = Matrix::parseMatrix($result1['matrix']);
        $result2 = Matrix::parseMatrix($result2['matrix']);

        $this->assertEquals($result, $result2);
    }
}
