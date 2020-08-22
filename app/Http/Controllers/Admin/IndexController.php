<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use PCIT\Framework\Http\Request;

class IndexController
{
    @@\Route('get', 'admin')
    public function __invoke()
    {
        $app_host = config('app.host');
        $content = <<<EOF
Create new GitHub App: $app_host/api/github/app/new

upload already exists GitHub App private key
EOF;

        return \Response::make($content, 200, ['Content-Type' => 'text/plain']);
    }

    @@\Route('get','admin/beta')
    @@\PCIT\Framework\Attributes\Middleware('Beta')
    public function beta(Request $request){
        return '//TODO';
    }
}
