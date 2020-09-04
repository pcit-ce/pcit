<?php

declare(strict_types=1);

namespace App\Http\Controllers\GitHub;

use PCIT\Framework\Attributes\Route;

class Detail
{
    #[Route('get', 'api/github/app')]
    public function __invoke()
    {
        $private_key_path = config('git.github.app.private_key_path');

        $jwt = \PCIT::github_apps_access_token()->getJWT($private_key_path);

        $result = \PCIT::github_apps()->getAppInfo($jwt);

        return \Response::make($result, 200, [
            'Content-Type' => 'application/json',
        ]);
    }
}
