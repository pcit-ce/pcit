<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

class IndexController
{
    public function __invoke(...$arg): void
    {
        view('builds/index.html');
        exit;
    }
}
