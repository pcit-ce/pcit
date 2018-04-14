<?php

namespace App\Http\Controllers\Profile;

class CodingController
{
    public function __invoke(...$arg)
    {
        echo $arg[0];
    }
}
