<?php

declare(strict_types=1);

namespace App\Http\Controllers\GitHub;

class Detail
{
    @@\Route('get', 'api/github/app')
    public function __invoke()
    {
        /** @var \PCIT\PCIT */
        $pcit = app('pcit');

        $private_key_path = config('git.github.app.private_key_path');

        $jwt = $pcit->github_apps_access_token->getJWT($private_key_path);

        $result = $pcit->github_apps->getAppInfo($jwt);

        return \Response::make($result, 200, [
            'Content-Type' => 'application/json',
        ]);
    }
}
