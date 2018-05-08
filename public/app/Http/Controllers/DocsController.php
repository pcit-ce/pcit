<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use KhsCI\Support\Response;

class DocsController
{
    public function __invoke(): void
    {
        Response::redirect('https://github.com/khs1994-php/khsci/tree/master/docs');
    }
}
