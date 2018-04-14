<?php

namespace App\Http\Controllers;

use KhsCI\Support\Response;

class StatusController
{
    public function __invoke()
    {
        Response::redirect('https://status.khs1994.com');
    }
}