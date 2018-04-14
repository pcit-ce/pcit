<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use KhsCI\Support\Response;

class StatusController
{
    public function __invoke(): void
    {
        Response::json([
            'code' => 0,
            'status' => 'https://status.khs1994.com',
        ]);
    }
}
