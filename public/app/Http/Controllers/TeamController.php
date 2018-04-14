<?php

namespace App\Http\Controllers;


class TeamController
{
    public function __invoke()
    {
        header('location: https://github.com/khs1994-php/khsci/graphs/contributors');
    }
}