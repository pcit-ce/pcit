<?php

declare(strict_types=1);

namespace PCIT\Framework\Storage;

use PCIT\Framework\Support\ServiceProvider;
use Pimple\Container;

class StorageServiceProvider extends ServiceProvider
{
    public function register(Container $pimple): void
    {
        $pimple['storage'] = function ($app) {
            return new Storage();
        };
    }
}
