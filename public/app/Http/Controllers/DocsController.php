<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class DocsController
{
    public function __invoke()
    {
        return [
            'code' => 200,
            'data' => 'https://github.com/khs1994-php/khsci/tree/master/docs',
        ];
    }
}
