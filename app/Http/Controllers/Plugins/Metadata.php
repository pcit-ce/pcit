<?php

declare(strict_types=1);

namespace App\Http\Controllers\Plugins;

use Symfony\Component\Finder\Finder;

class Metadata
{
    @@\Route('get', 'plugins/metadata')
    public function __invoke()
    {
        $plugin_metadata_path = base_path().'config/plugin.metadata.json';

        if (file_exists($plugin_metadata_path) and \PHP_SAPI !== 'cli') {
            return \Response::make(file_get_contents($plugin_metadata_path), 200, [
                'Content-type' => 'application/json',
            ]);
        }

        $result = Finder::create()
            ->in(base_path().'plugins')
            ->name('metadata.json');

        $arr = [];

        foreach ($result as $item) {
            $arr[] = json_decode($item->getContents());
        }

        if (\PHP_SAPI === 'cli') {
            file_put_contents($plugin_metadata_path, json_encode($arr));

            return;
        }

        return $arr;
    }
}
