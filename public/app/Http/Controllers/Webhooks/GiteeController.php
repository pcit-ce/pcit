<?php

namespace App\Http\Controllers\Webhooks;


class GiteeController
{
    public function __invoke()
    {
        file_put_contents('C:/1', file_get_contents("php://input"));
    }
}
