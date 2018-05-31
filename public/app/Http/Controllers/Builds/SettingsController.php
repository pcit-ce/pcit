<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Http\Controllers\APITokenController;
use App\Setting;
use Exception;

class SettingsController
{
    /**
     * Returns a list of the settings for that repository.
     *
     * /repo/{repository.id}/settings
     *
     * @param array $args
     *
     * @return array|string
     *
     * @throws Exception
     */
    public function __invoke(...$args)
    {
        list($rid, $git_type, $uid) = APITokenController::checkByRepo(...$args);

        return Setting::list($git_type, $rid);
    }

    /**
     * Returns a single setting.
     *
     * /repo/{repository.id}/setting/{setting.name}
     *
     * @param array $args
     *
     * @return array|string
     *
     * @throws Exception
     */
    public function get(...$args)
    {
        list($username, $repo_name, $setting_name) = $args;

        list($rid, $git_type, $uid) = APITokenController::checkByRepo(...$args);

        return Setting::get($git_type, $rid, $setting_name);
    }

    /**
     * Updates a single setting.
     *
     * patch
     *
     * /repo/{repository.id}/setting/{setting.name}
     *
     * <pre>
     * ['setting.value'=>true]
     *
     * { "setting.value": true }
     * </pre>
     *
     * @param array $args
     *
     * @return int
     *
     * @throws Exception
     */
    public function update(...$args)
    {
        list($username, $repo_name, $setting_name) = $args;

        list($rid, $git_type, $uid) = APITokenController::checkByRepo(...$args);

        $json = file_get_contents('php://input');

        foreach (json_decode($json, true) as $k => $v) {
            $setting_name = explode('.', $k)[1];
            $setting_value = $v;

            return Setting::update($git_type, $rid, $setting_name, $setting_value);
        }

        throw new Exception('', 500);
    }
}
