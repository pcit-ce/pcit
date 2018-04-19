<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use KhsCI\Support\Response;

class TeamController
{
    public function __invoke()
    {
        return [
            'code' => 200,
            'team' => 'https://github.com/khs1994-php/khsci/graphs/contributors',
        ];
    }
}
