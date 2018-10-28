<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use PCIT\PCIT;

class HelloController
{
    public function registry(): void
    {
        (new PCIT())->tencent_ai->face()->add();
    }

    public function verify(): void
    {
        (new PCIT())->tencent_ai->face()->add();
    }
}
