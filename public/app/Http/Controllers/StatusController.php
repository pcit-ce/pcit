<?php

namespace App\Http\Controllers;

class StatusController
{
    public function __invoke()
    {
        header('location: https://status.khs1994.com');
    }
}