<?php

declare(strict_types=1);

use Tests\TestCase;

class MetadataTest extends TestCase
{
    public function test(): void
    {
        $response = $this->get('plugins/metadata');

        file_put_contents(base_path().'config/plugin.metadta.json', $response->getContent());

        $this->assertTrue(true);
    }
}
