<?php

declare(strict_types=1);

namespace KhsCI\Service\Build;

use Docker\Docker;
use Exception;
use KhsCI\Support\Cache;

class ServicesClient
{
    /**
     * 运行服务.
     *
     * @param array  $service
     * @param int    $job_id
     * @param array  $config
     * @param Docker $docker
     *
     * @throws Exception
     */
    public static function config(?array $service, int $job_id, ?array $config, Docker $docker): void
    {
        if (null === $service) {
            return;
        }

        foreach ($service as $service_name => $array) {
            $image = $array['image'];
            $env = $array['environment'] ?? null;
            $entrypoint = $array['entrypoint'] ?? null;
            $command = $array['command'] ?? null;

            $image = ParseClient::image($image, $config);

            $docker_container = $docker->container;

            $container_config = $docker_container
                ->setEnv($env)
                ->setEntrypoint($entrypoint)
                ->setLabels([
                    'com.khs1994.ci.service' => $job_id,
                    'com.khs1994.ci.service.name' => $service_name,
                    'com.khs1994.ci' => $job_id,
                ])
                ->setImage($image)
                ->setCmd($command)
                ->setNetworkingConfig([
                    'EndpointsConfig' => [
                        "$job_id" => [
                            'Aliases' => [
                                $service_name,
                            ],
                        ],
                    ],
                ])
                ->setCreateJson(null)
                ->getCreateJson();

            Cache::connect()->lPush((string) $job_id.'_services', $container_config);
        }
    }
}
