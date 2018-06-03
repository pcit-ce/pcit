<?php

namespace App\Http\Controllers\Users;

use KhsCI\KhsCI;

class HelloController
{
    public function registry()
    {
        (new KhsCI())->tencent_ai->face()->add();
    }

    public function verify()
    {
        (new KhsCI())->tencent_ai->face()->add();
    }
}
