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
        ], [
            'PCIT_COMMIT=fa65eed5098221166a6507d64ab792fc2ae69b13',
            'PCIT_BUILD_ID=100',
        ]
    );

        $this->assertEquals(
        'INPUT_LOCAL_DIR=fa65eed5098221166a6507d64ab792fc2ae69b13',
        $result[3]
    );
    }
}
