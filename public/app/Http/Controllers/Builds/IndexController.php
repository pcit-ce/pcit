<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

class IndexController
{
    public function __invoke(...$arg): void
    {
        require __DIR__.'/../../../../public/builds/index.html';
        exit;
    }

    public function repo(): void
    {
        require __DIR__.'/../../../../public/repo/index.html';
        exit;
    }
}
