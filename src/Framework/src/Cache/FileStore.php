<?php

declare(strict_types=1);

namespace PCIT\Framework\Cache;

class FileStore
{
    public function __call($method, $arguments): void
    {
        file_put_contents('cache.txt', json_encode(
           [$method => $arguments]
       ));
    }
}
