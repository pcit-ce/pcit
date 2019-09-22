<?php

declare(strict_types=1);

namespace PCIT\Builder\Tests\Events;

use PCIT\Builder\Events\Matrix;
use Symfony\Component\Yaml\Yaml;
use Tests\PCITTestCase;

class MatrixTest extends PCITTestCase
{
    public function test_parseMatrix(): void
    {
        $yaml = <<<EOF
matrix:
  PHP_VERSION:
    - 7.2.16
    - 7.1.27
  REDIS_VERSION:
    - 5.0.3

EOF;

        $yaml2 = <<<EOF
matrix:
  include:
    - PHP_VERSION: 7.2.16
      REDIS_VERSION: 5.0.3
    - PHP_VERSION: 7.1.27
      REDIS_VERSION: 5.0.3
EOF;

        $result1 = Yaml::parse($yaml);
        $result2 = Yaml::parse($yaml2);

        $result = Matrix::parseMatrix($result1['matrix']);
        $result2 = Matrix::parseMatrix($result2['matrix']);

        $this->assertEquals($result, $result2);
    }
}
