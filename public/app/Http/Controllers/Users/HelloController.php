<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use KhsCI\KhsCI;

class HelloController
{
    public function registry(): void
    {
        (new KhsCI())->tencent_ai->face()->add();
    }

    public function verify(): void
    {
        (new KhsCI())->tencent_ai->face()->add();
    }
}
