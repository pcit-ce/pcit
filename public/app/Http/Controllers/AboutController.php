<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class AboutController
{
    public function __invoke()
    {
        return [
            'code' => 200,
            'about' => 'The goal of KhsCI is to build CI/CD System by PHP Powered by Docker and Kubernetes',
        ];
    }
}
