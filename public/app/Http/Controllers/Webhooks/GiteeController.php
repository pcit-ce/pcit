<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

class GiteeController
{
    public function __invoke(): void
    {
        file_put_contents('C:/1', file_get_contents('php://input'));
    }
}
