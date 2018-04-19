<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class StatusController
{
    public function __invoke()
    {
        return [
            'code' => 200,
            'status' => 'https://status.khs1994.com',
        ];
    }
}
