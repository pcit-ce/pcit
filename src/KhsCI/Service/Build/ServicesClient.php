<?php

declare(strict_types=1);

namespace KhsCI\Service\Build;

use Docker\Docker;
use Exception;
use KhsCI\Support\Log;

class ServicesClient
{
    /**
     * 运行服务.
     *
     * @param array  $service
     * @param string $unique_id
     * @param array  $config
     * @param Docker $docker
     *
     * @throws Exception
     */
    public static function runService(?array $service, string $unique_id, ?array $config, Docker $docker): void
    {
        if (null === $service) {
            return;
        }

        $client = new Client();

        foreach ($service as $service_name => $array) {
            $client->cancel();

            $image = $array['image'];
            $env = $array['environment'] ?? null;
            $entrypoint = $array['entrypoint'] ?? null;
            $command = $array['command'] ?? null;

            $image = $client->parseImage($image, $config);

            $docker_image = $docker->image;
            $docker_container = $docker->container;

            $tag = explode(':', $image)[1] ?? 'latest';

            $docker_image->pull($image, $tag);

            $container_id = $docker_container
                ->setEnv($env)
                ->setEntrypoint($entrypoint)
                ->setHostConfig(null, $unique_id)
                ->setLabels(['com.khs1994.ci.service' => $unique_id])
                ->create($image, $service_name, $command);

            $docker_container->start($container_id);

            Log::debug(
                __FILE__,
                __LINE__,
                "Run $service_name By Image $image, Container Id Is $container_id",
                [],
                Log::EMERGENCY
            );
        }
    }
}
