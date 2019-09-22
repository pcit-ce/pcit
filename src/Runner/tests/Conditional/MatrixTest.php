<?php

declare(strict_types=1);

namespace PCIT\Builder\Tests\Conditional;

use PCIT\Builder\Conditional\Matrix;
use Symfony\Component\Yaml\Yaml;
use Tests\PCITTestCase;

class MatrixTest extends PCITTestCase
{
    public function test(): void
    {
        $conditional_yaml = <<<EOF
when:
  matrix:
    - PHP_VERSION: 7.2.16
      NGINX_VERSION: 1.15.9
      REDIS_VERSION: 5.0.3
      MYSQL_VERSION: 5.7.23
EOF;

        $current_yaml = <<<EOF
matrix:
  PHP_VERSION:
    - 7.2.16
  NGINX_VERSION:
    - 1.15.9
  REDIS_VERSION:
    - 5.0.3
  MYSQL_VERSION:
    - 5.7.23
EOF;
        $conditional = Yaml::parse($conditional_yaml);
        $conditional = $conditional['when']['matrix'];

        $current = Yaml::parse($current_yaml);
        $current = $current['matrix'];
        $current = \PCIT\Builder\Events\Matrix::parseMatrix($current);

        $result = (new Matrix($conditional, $current[0]))->handle();

        $this->assertTrue($result);
    }
}
