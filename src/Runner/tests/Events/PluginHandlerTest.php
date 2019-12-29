<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Events;

use PCIT\Runner\Events\PluginHandler;
use PHPUnit\Framework\TestCase;

class PluginHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $result = (new PluginHandler())->handle(
        [
            'upload_dir' => '${PCIT_BUILD_ID}/ui/nightly/${PCIT_COMMIT}',
            'local_dir' => '${PCIT_COMMIT}',
            'bucket' => '$PCIT_COMMIT',
            'bool_true' => true,
            'bool_false' => false,
            'var_array' => ['a', 'b'],
            'var_obj' => ['k1' => 'v1', 'k2' => 'v2'],
        ], [
            'PCIT_COMMIT=fa65eed5098221166a6507d64ab792fc2ae69b13',
            'PCIT_BUILD_ID=100',
        ]
    );

        // var_dump($result);

        $this->assertEquals(
        'INPUT_LOCAL_DIR=fa65eed5098221166a6507d64ab792fc2ae69b13',
        $result[3]
    );

        $this->assertEquals(
        'INPUT_BOOL_TRUE=true',
        $result[5]
    );

        $this->assertEquals(
        'INPUT_BOOL_FALSE=false',
        $result[6]
    );

        $this->assertEquals(
        'INPUT_VAR_ARRAY=a,b',
        $result[7]
    );

        $this->assertEquals(
        'INPUT_VAR_OBJ={"k1":"v1","k2":"v2"}',
        $result[8]
    );
    }
}
