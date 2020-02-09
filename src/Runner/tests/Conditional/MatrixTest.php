<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Conditional;

use PCIT\Runner\Conditional\Matrix;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class MatrixTest extends TestCase
{
    public function test(): void
    {
        $conditional_yaml = <<<EOF
when:
  matrix:
    - PHP_VERSION: 7.4.2
      NGINX_VERSION: 1.15.9
      REDIS_VERSION: 5.0.3
      MYSQL_VERSION: 5.7.23
EOF;

        $current_yaml = <<<EOF
matrix:
  PHP_VERSION:
    - 7.4.2
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
        $current = \PCIT\Runner\Events\Matrix::parseMatrix($current);

        $result = (new Matrix($conditional, $current[0]))->handle();

        $this->assertTrue($result);
    }
}
