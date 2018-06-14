<?php

declare(strict_types=1);

namespace App\Http\Controllers\System;

use App\Http\Controllers\APITokenController;
use Exception;
use KhsCI\Support\Env;

class SystemController
{
    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function getOAuthClientId()
    {
        $git_type = (APITokenController::getUser())[0];

        switch ($git_type) {
            case 'github':
                $url = 'https://github.com/settings/connections/applications/';

                break;
        }

        return $url.Env::get('CI_'.strtoupper($git_type).'_CLIENT_ID');
    }

    public function getGitHubAppSettingsUrl(string $org_name = null)
    {
        $url = 'https://github.com/settings/installations';

        if ('null' === strtolower($org_name)) {
            $org_name = null;
        }

        if ($org_name) {

            $url = "https://github.com/organizations/{$org_name}/settings/installations";
        }

        return $url;
    }

    public function getGitHubAppInstallationUrl($uid)
    {
        $app_name = strtolower(Env::get('CI_GITHUB_APP_NAME'));

        $url = "https://github.com/apps/{$app_name}/installations/new/permissions?suggested_target_id=".$uid;

        return $url;
    }
}
