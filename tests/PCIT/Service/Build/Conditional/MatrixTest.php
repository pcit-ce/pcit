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
        $current_yaml = <<<EOF
when:
  matrix:
    - PHP_VERSION: 7.2.11
      NGINX_VERSION: 1.15.6
      REDIS_VERSION: 5.0.0
      MYSQL_VERSION: 5.7.23
EOF;

        $conditional_yaml = <<<EOF
matrix:
  PHP_VERSION:
    - 7.2.11
  NGINX_VERSION:
    - 1.15.6
  REDIS_VERSION:
    - 5.0.0
  MYSQL_VERSION:
    - 5.7.23
EOF;
        $current = Yaml::parse($current_yaml);
        $current = $current['when']['matrix'];

        $conditional = Yaml::parse($conditional_yaml);
        $conditional = $conditional['matrix'];
        $conditional = \PCIT\Service\Build\Events\Matrix::parseMatrix($conditional);

        $result = (new Matrix($conditional, $current))->handle();

        $this->assertTrue($result);
    }
}
