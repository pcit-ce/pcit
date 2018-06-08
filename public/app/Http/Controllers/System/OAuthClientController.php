<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\APITokenController;
use Exception;
use KhsCI\Support\Env;

class OAuthClientController
{
    /**
     * @return mixed
     * @throws Exception
     */
    public function __invoke()
    {
        $git_type = (APITokenController::getUser())[0];

        switch ($git_type) {
            case 'github':
                $url = 'https://github.com/settings/connections/applications/';

                break;
        }

        return $url.Env::get('CI_'.strtoupper($git_type).'_CLIENT_ID');
    }
}
