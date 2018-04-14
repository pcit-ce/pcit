<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use KhsCI\Support\Response;

class AboutController
{
    public function __invoke(): void
    {
        Response::json([
            'code' => 0,
            'about' => 'The goal of KhsCI is to build CI/CD System by PHP Powered by Docker and Kubernetes',
        ]);
    }
}
