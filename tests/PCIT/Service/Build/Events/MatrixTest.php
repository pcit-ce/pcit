<?php

declare(strict_types=1);

namespace PCIT\Tests\Service\Build\Events;

use PCIT\Service\Build\Events\Matrix;
use PCIT\Tests\PCITTestCase;
use Symfony\Component\Yaml\Yaml;

class MatrixTest extends PCITTestCase
{
    public function test_parseMatrix(): void
    {
        $yaml = <<<EOF
matrix:
  PHP_VERSION: 
    - 7.2.11
    - 7.1.23
  REDIS_VERSION:
    - 5.0.0  
  
EOF;

        $yaml2 = <<<EOF
matrix:
  include:
    - PHP_VERSION: 7.2.11
      REDIS_VERSION: 5.0.0
    - PHP_VERSION: 7.1.23
      REDIS_VERSION: 5.0.0 
EOF;

        $array1 = Yaml::parse($yaml);
        $array2 = Yaml::parse($yaml2);

        $result = Matrix::parseMatrix($array1['matrix']);
        $result2 = Matrix::parseMatrix($array2['matrix']);

        $this->assertEquals($result, $result2);
    }
}
