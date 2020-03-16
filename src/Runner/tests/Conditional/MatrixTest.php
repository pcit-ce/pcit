<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Conditional;

use JsonSchema\Constraints\BaseConstraint;
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
    - PHP_VERSION: 7.3.2
      MYSQL_VERSION: 5.7.23
      NGINX_VERSION: 1.15.9
      REDIS_VERSION: 5.0.7
    - PHP_VERSION: 7.4.2
      MYSQL_VERSION: 5.7.23
      NGINX_VERSION: 1.15.9
      REDIS_VERSION: 5.0.7
EOF;

        $current = [
            'PHP_VERSION' => '7.4.2',
            'NGINX_VERSION' => '1.15.9',
            'REDIS_VERSION' => '5.0.7',
            'MYSQL_VERSION' => '5.7.23',
        ];

        $conditional = BaseConstraint::arrayToObjectRecursive(
            Yaml::parse($conditional_yaml));
        $conditional = $conditional->when->matrix;

        $result = (new Matrix($conditional, $current))->handle();

        $this->assertTrue($result);
    }

    public function testInclude(): void
    {
        $conditional_yaml = <<<EOF
when:
  matrix:
    include:
    - PHP_VERSION: 7.3.2
      MYSQL_VERSION: 5.7.23
      NGINX_VERSION: 1.15.9
      REDIS_VERSION: 5.0.7
    - PHP_VERSION: 7.4.2
      MYSQL_VERSION: 5.7.23
      NGINX_VERSION: 1.15.9
      REDIS_VERSION: 5.0.7
EOF;

        $current = [
            'PHP_VERSION' => '7.4.2',
            'NGINX_VERSION' => '1.15.9',
            'REDIS_VERSION' => '5.0.7',
            'MYSQL_VERSION' => '5.7.23',
        ];

        $conditional = BaseConstraint::arrayToObjectRecursive(
            Yaml::parse($conditional_yaml));
        $conditional = $conditional->when->matrix;

        $result = (new Matrix($conditional, $current))->handle();

        $this->assertTrue($result);
    }

    public function testExclude(): void
    {
        $conditional_yaml = <<<EOF
when:
  matrix:
    exclude:
    - PHP_VERSION: 7.3.2
      MYSQL_VERSION: 5.7.23
      NGINX_VERSION: 1.15.9
      REDIS_VERSION: 5.0.7
    - PHP_VERSION: 7.4.2
      MYSQL_VERSION: 5.7.23
      NGINX_VERSION: 1.15.9
      REDIS_VERSION: 5.0.7
EOF;

        $current = [
            'PHP_VERSION' => '7.4.2',
            'NGINX_VERSION' => '1.15.9',
            'REDIS_VERSION' => '5.0.7',
            'MYSQL_VERSION' => '5.7.23',
        ];

        $conditional = BaseConstraint::arrayToObjectRecursive(
            Yaml::parse($conditional_yaml));
        $conditional = $conditional->when->matrix;

        $result = (new Matrix($conditional, $current))->handle();

        $this->assertFalse($result);
    }

    public function testExclude2(): void
    {
        $conditional_yaml = <<<EOF
when:
  matrix:
    exclude:
    - PHP_VERSION: 7.3.2
      MYSQL_VERSION: 5.7.23
      NGINX_VERSION: 1.15.9
      REDIS_VERSION: 5.0.7
EOF;

        $current = [
            'PHP_VERSION' => '7.4.2',
            'NGINX_VERSION' => '1.15.9',
            'REDIS_VERSION' => '5.0.7',
            'MYSQL_VERSION' => '5.7.23',
        ];

        $conditional = BaseConstraint::arrayToObjectRecursive(
            Yaml::parse($conditional_yaml));
        $conditional = $conditional->when->matrix;

        $result = (new Matrix($conditional, $current))->handle();

        $this->assertTrue($result);
    }
}
