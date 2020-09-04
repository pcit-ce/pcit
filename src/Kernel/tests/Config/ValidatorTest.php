<?php

declare(strict_types=1);

namespace PCIT\Tests\Config;

use JsonSchema\Constraints\BaseConstraint;
use PCIT\Config\Validator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class ValidatorTest extends TestCase
{
    public function test_validate(): void
    {
        $result = (new Validator())->validate($this->toObj(
            base_path('.pcit_error/.pcit.yaml')
        ));

        $this->assertNotEquals([], $result);
    }

    public function toObj(string $path)
    {
        return BaseConstraint::arrayToObjectRecursive(Yaml::parse(
            file_get_contents($path)
        ));
    }
}
