<?php

declare(strict_types=1);

namespace App\Http\controllers\Users;

class LoginController
{
    public function index(): void
    {
        require_once __DIR__.'/../../../../resources/views/login.html';
    }
}
