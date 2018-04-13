<?php

namespace App\Http\Controllers\Test;

class TestController
{
    public function test()
    {
        header('Location:/index.html');
    }
}