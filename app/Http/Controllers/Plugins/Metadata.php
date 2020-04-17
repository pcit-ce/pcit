<?php

declare(strict_types=1);

namespace App\Http\Controllers\Plugins;

use Symfony\Component\Finder\Finder;

class Metadata
{
    public function __invoke()
    {
        $result = Finder::create()
        ->in(base_path().'plugins')
        ->name('metadata.json');

        $arr = [];

        foreach ($result as $item) {
            $arr[] = json_decode($item->getContents());
        }

        return $arr;
    }
}
