<?php

namespace App\Http\Controllers;

use KhsCI\Support\Response;

class TeamController
{
    public function __invoke()
    {
        Response::redirect('https://github.com/khs1994-php/khsci/graphs/contributors');
    }
}