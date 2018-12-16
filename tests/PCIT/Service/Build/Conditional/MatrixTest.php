<?php

declare(strict_types=1);

namespace PCIT\Tests\Service\Build\Conditional;

use PCIT\Service\Build\Conditional\Matrix;
use PCIT\Tests\PCITTestCase;
use Symfony\Component\Yaml\Yaml;

class MatrixTest extends PCITTestCase
{
    public function test(): void
    {
        $conditional_yaml = <<<EOF
when:
  matrix:
    - PHP_VERSION: 7.2.13
      NGINX_VERSION: 1.15.6
      REDIS_VERSION: 5.0.0
      MYSQL_VERSION: 5.7.23
EOF;

        $current_yaml = <<<EOF
matrix:
  PHP_VERSION:
    - 7.2.13
  NGINX_VERSION:
    - 1.15.6
  REDIS_VERSION:
    - 5.0.0
  MYSQL_VERSION:
    - 5.7.23
EOF;
        $conditional = Yaml::parse($conditional_yaml);
        $conditional = $conditional['when']['matrix'];

        $current = Yaml::parse($current_yaml);
        $current = $current['matrix'];
        $current = \PCIT\Service\Build\Events\Matrix::parseMatrix($current);

        $result = (new Matrix($conditional, $current[0]))->handle();

        $this->assertTrue($result);
    }
}
