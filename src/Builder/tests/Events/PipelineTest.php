<?php

declare(strict_types=1);

namespace PCIT\Builder\Tests\Events;

use PCIT\Builder\Events\Pipeline;
use PHPUnit\Framework\TestCase;

class PipelineTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_handleDeployer(): void
    {
        $result = (new Pipeline(null, null, null, null))
            ->handleDeployer(
                [
                    'provider' => 's3',
                    'upload_dir' => '${PCIT_BUILD_ID}/ui/nightly/${PCIT_COMMIT}',
                    'local_dir' => '${PCIT_COMMIT}',
                    'bucket' => '$PCIT_COMMIT',
                ], [
                    'PCIT_COMMIT=fa65eed5098221166a6507d64ab792fc2ae69b13',
                    'PCIT_BUILD_ID=100',
                ]
            );

        $this->assertEquals(
            'PCIT_S3_LOCAL_DIR=fa65eed5098221166a6507d64ab792fc2ae69b13',
            $result['env'][5]
        );
    }
}
