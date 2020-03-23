<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Agent\Docker;

use PCIT\Runner\Agent\Docker\DockerHandler;
use Tests\TestCase;

class DockerHandlerTest extends TestCase
{
    // public function test_handle_artifact_upload(): void
    // {
    //     $result = (new DockerHandler())->handleArtifact(
    //    1,json_encode([
    //       'Image' => 'pcit/upload-artifact',
    //       'Env' => [
    //           'INPUT_NAME=dist',
    //           'INPUT_PATH=dist',
    //       ],
    //    ])
    // );

    //     var_dump($result);
    // }

    public function test_handle_artifact_exclude(): void
    {
        $result = (new DockerHandler())->handleArtifact(1, json_encode([
          'Image' => 'pcit/xxx-artifact',
          'Env' => [
              'INPUT_NAME=dist',
              'INPUT_PATH=dist',
              ],
          ])
        );

        // var_dump($result);

        $this->assertEquals('pcit/xxx-artifact', json_decode($result)->Image);
    }
}
