<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Agent\Docker;

use PCIT\Runner\Agent\Docker\ExpressionHandler;
use PHPUnit\Framework\TestCase;

class ExpressionHandlerTest extends TestCase
{
    public function test_handleOutput(): void
    {
        $string = '${{ steps.scripts.outputs }}--${{ steps.scripts.outputs.output }}--${{ steps.scripts.outputs.outputs }}';
        $result = (new ExpressionHandler())->handleOutput($string, [
          'scripts' => [
              'output' => 'value',
          ],
      ]);

        $this->assertEquals('{"output":"value"}--value--', $result);
    }
}
