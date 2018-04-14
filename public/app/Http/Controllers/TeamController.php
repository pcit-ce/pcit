<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use KhsCI\Support\Response;

class TeamController
{
    public function __invoke(): void
    {
        Response::json([
            'code' => 0,
            'team' => 'https://github.com/khs1994-php/khsci/graphs/contributors',
        ]);
    }
}
