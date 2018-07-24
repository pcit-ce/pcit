<?php

declare(strict_types=1);

namespace KhsCI\Service\Build;

use Docker\Container\Client as Container;
use Exception;
use KhsCI\Support\Cache;
use KhsCI\Support\CI;
use KhsCI\Support\Git;

class GitClient
{
    private static function parseGit()
    {
        $git_config = [];

        $depth = $git['depth'] ?? 10;
        $recursive = $git['recursive'] ?? false;
        $skip_verify = $git['skip_verify'] ?? false;
        $tags = $git['tags'] ?? false;
        $submodule_override = $git['submodule_override'] ?? null;
        $hosts = $git['hosts'] ?? [];
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

        return [$git_config, $git_image, $hosts];
    }

    /**
     * @param array     $git
     * @param string    $git_type
     * @param string    $event_type
     * @param string    $repo_full_name
     * @param string    $workdir
     * @param string    $commit_id
     * @param string    $branch
     * @param Container $docker_container
     * @param int       $build_key_id
     * @param Client    $client
     *
     * @throws Exception
     *
     * @see https://github.com/drone-plugins/drone-git
     */
    public static function config(?array $git,
                                  string $git_type,
                                  string $event_type,
                                  string $repo_full_name,
                                  string $workdir,
                                  string $commit_id,
                                  string $branch,
                                  Container $docker_container,
                                  int $build_key_id,
                                  Client $client): void
    {
        $git_image = 'plugins/git';
        $git_config = [];
        $hosts = [];
        $git_env = null;

        if ($git) {
            list($git_config, $git_image, $hosts) = self::parseGit();
        }

        $git_url = Git::getUrl($git_type, $repo_full_name);

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
                    'DRONE_COMMIT_REF=refs/pull/'.$client->pull_number.'/head',
                ], $git_config);

                break;
            case  CI::BUILD_EVENT_TAG:
                $git_env = array_merge([
                    'DRONE_REMOTE_URL='.$git_url,
                    'DRONE_WORKSPACE='.$workdir,
                    'DRONE_BUILD_EVENT=tag',
                    'DRONE_COMMIT_SHA='.$commit_id,
                    'DRONE_COMMIT_REF=refs/tags/'.$client->tag,
                ], $git_config);

                break;
        }

        $config = $docker_container
            ->setEnv($git_env)
            ->setLabels(['com.khs1994.ci.git' => $build_key_id, 'com.khs1994.ci' => $build_key_id])
            ->setBinds(["$build_key_id:$workdir"])
            ->setExtraHosts($hosts)
            ->setImage($git_image)
            ->setCreateJson(null)
            ->getCreateJson();

        Cache::connect()->lpush($build_key_id, json_encode($config));
    }
}
