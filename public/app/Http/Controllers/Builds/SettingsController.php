<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

class SettingsController
{
    /**
     * Returns a list of the settings for that repository.
     *
     * /repo/{repository.id}/settings
     *
     * @param $git_type
     * @param $username
     * @param $repo_name
     */
    public function __invoke(string $git_type, string $username, string $repo_name): void
    {
    }

    /**
     * Returns a single setting.
     *
     * /repo/{repository.id}/setting/{setting.name}
     *
     * @param $git_type
     * @param $username
     * @param $repo_name
     * @param $setting_name
     */
    public function get(string $git_type, string $username, string $repo_name, string $setting_name): void
    {
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
     * @param string $git_type
     * @param string $username
     * @param string $repo_name
     * @param string $seeing_name
     */
    public function update(string $git_type, string $username, string $repo_name, string $seeing_name): void
    {
        $json = file_get_contents('php://input');
    }
}
