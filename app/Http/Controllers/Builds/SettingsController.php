<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Http\Controllers\Users\JWTController;
use App\Setting;
use Exception;

class SettingsController
{
    /**
     * Returns a list of the settings for that repository.
     *
     * /repo/{repository.slug}/settings
     *
     * @param array $args
     *
     * @return array|string
     *
     * @throws Exception
     */
    public function __invoke(...$args)
    {
        list($rid, $git_type, $uid) = JWTController::checkByRepo(...$args);

        $result = Setting::list($rid, $git_type)[0] ?? [];

        return $result;
    }

    /**
     * Returns a single setting.
     *
     * /repo/{repository.slug}/setting/{setting.name}
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

        list($rid, $git_type, $uid) = JWTController::checkByRepo(...$args);

        return Setting::get($rid, $setting_name, $git_type);
    }

    /**
     * Updates a single setting.
     *
     * patch
     *
     * /repo/{repository.slug}/setting/{setting.name}
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
        $request = app('request');

        list($username, $repo_name, $setting_name) = $args;

        list($rid, $git_type, $uid) = JWTController::checkByRepo(...$args);

        // $json = file_get_contents('php://input');
        $json = $request->getContent();

        foreach (json_decode($json, true) as $k => $v) {
            $setting_value = $v;

            return Setting::update($rid, $setting_name, (string) $setting_value, $git_type);
        }

        throw new Exception('', 500);
    }
}
