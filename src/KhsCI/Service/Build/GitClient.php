<?php

declare(strict_types=1);

namespace KhsCI\Service\Build;

use Docker\Container\Client as Container;
use Exception;
use KhsCI\Support\CI;
use KhsCI\Support\Git;
use KhsCI\Support\Log;

class GitClient
{
    /**
     * @param array     $git
     * @param string    $git_type
     * @param string    $event_type
     * @param string    $repo_full_name
     * @param string    $workdir
     * @param string    $commit_id
     * @param string    $branch
     * @param string    $unique_id
     * @param Container $docker_container
     * @param int       $build_key_id
     *
     * @throws Exception
     *
     * @see https://github.com/drone-plugins/drone-git
     */
    public static function runGit(?array $git,
                                  string $git_type,
                                  string $event_type,
                                  string $repo_full_name,
                                  string $workdir,
                                  string $commit_id,
                                  string $branch,
                                  string $unique_id,
                                  Container $docker_container,
                                  int $build_key_id): void
    {
        $git_image = 'plugins/git';

        $git_config = [];

        $hosts = [];

        if ($git) {
            $depth = $git['depth'] ?? 10;
            $recursive = $git['recursive'] ?? false;
            $skip_verify = $git['skip_verify'] ?? false;
            $tags = $git['tags'] ?? false;
            $submodule_override = $git['submodule_override'] ?? null;
            $hosts = $git['hosts'];
            // 防止用户传入 false
            if ($depth) {
                array_push($git_config, "PLUGIN_DEPTH=$depth");
            } else {
                array_push($git_config, 'PLUGIN_DEPTH=2');
            }

            $recursive && array_push($git_config, 'PLUGIN_RECURSIVE=true');

            $skip_verify && array_push($git_config, 'PLUGIN_SKIP_VERIFY=true');

            $tags && array_push($git_config, 'PLUGIN_TAGS=true');

            $submodule_override && array_push(
                $git_config, 'PLUGIN_SUBMODULE_OVERRIDE='.json_encode($submodule_override)
            );

            $git_image = $git['image'] ?? 'plugins/git';
        }

        $client = new Client();

        $git_url = Git::getUrl($git_type, $repo_full_name);

        $git_env = null;

        switch ($event_type) {
            case CI::BUILD_EVENT_PUSH:
                $git_env = array_merge([
                    'DRONE_REMOTE_URL='.$git_url,
                    'DRONE_WORKSPACE='.$workdir,
                    'DRONE_BUILD_EVENT=push',
                    'DRONE_COMMIT_SHA='.$commit_id,
                    'DRONE_COMMIT_REF='.'refs/heads/'.$branch,
                ], $git_config);

                break;
            case CI::BUILD_EVENT_PR:
                $git_env = array_merge([
                    'DRONE_REMOTE_URL='.$git_url,
                    'DRONE_WORKSPACE='.$workdir,
                    'DRONE_BUILD_EVENT=pull_request',
                    'DRONE_COMMIT_SHA='.$commit_id,
                    'DRONE_COMMIT_REF=refs/pull/'.$client->pull_id.'/head',
                ], $git_config);

                break;
            case  CI::BUILD_EVENT_TAG:
                $git_env = array_merge([
                    'DRONE_REMOTE_URL='.$git_url,
                    'DRONE_WORKSPACE='.$workdir,
                    'DRONE_BUILD_EVENT=tag',
                    'DRONE_COMMIT_SHA='.$commit_id,
                    'DRONE_COMMIT_REF=refs/tags/'.$client->tag_name,
                ], $git_config);

                break;
        }

        $container_id = $docker_container
            ->setEnv($git_env)
            ->setLabels(['com.khs1994.ci.git' => $unique_id])
            ->setBinds(["$unique_id:$workdir"])
            ->setExtraHosts($hosts)
            ->setImage($git_image)
            ->create()
            ->start(null);

        Log::debug(
            __FILE__,
            __LINE__,
            'Run Git Clone Container By Image '.$git_image.', Container Id is '.$container_id,
            [],
            Log::EMERGENCY
        );

        $client->docker_container_logs($build_key_id, $docker_container, $container_id);
    }
}
