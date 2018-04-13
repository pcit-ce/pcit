<?php

declare(strict_types=1);

namespace App\Http\Controllers\Test;

class TestController
{
    public function test(): void
    {
        header('Location:/index.html');
    }
}
