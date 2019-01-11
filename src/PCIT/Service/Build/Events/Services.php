<?php

declare(strict_types=1);

namespace PCIT\Service\Build\Events;

use App\Job;
use Docker\Container\Client;
use Exception;
use PCIT\PCIT;
use PCIT\Service\Build\Parse;
use PCIT\Support\Cache;
use PCIT\Support\CacheKey;

class Services
{
    private $job_id;

    private $service;

    private $matrix_config;

    public function __construct($service, int $job_id, ?array $matrix_config)
    {
        $this->service = $service;
        $this->job_id = $job_id;
        $this->matrix_config = $matrix_config;
    }

    /**
     * 运行服务.
     *
     * @throws Exception
     */
    public function handle(): void
    {
        if (null === $this->service) {
            return;
        }

        foreach ($this->service as $service_name => $array) {
            list(
                'image' => $image,
                'env' => $env,
                'entrypoint' => $entrypoint,
                'commands' => $commands
                ) = ServiceDefault::handle($service_name);

            if (\is_array($array)) {
                $image = $array->image ?? $image;
                $env = $array->environment ?? $env;
                $entrypoint = $array->entrypoint ?? $entrypoint;
                $commands = $array->commands ?? $array->command ?? $commands;

                $image = Parse::image($image, $this->matrix_config);
            }

            /**
             * @var Client
             */
            $docker_container = app(PCIT::class)->docker->container;

            $container_config = $docker_container
                ->setEnv($env)
                ->setEntrypoint($entrypoint)
                ->setLabels([
                    'com.khs1994.ci.service' => (string) $this->job_id,
                    'com.khs1994.ci.service.name' => $service_name,
                    'com.khs1994.ci' => (string) $this->job_id,
                ])
                ->setImage($image)
                ->setCmd($commands)
                ->setNetworkingConfig([
                    'EndpointsConfig' => [
                        "$this->job_id" => [
                            'Aliases' => [
                                $service_name,
                            ],
                        ],
                    ],
                ])
                ->setCreateJson(null)
                ->getCreateJson();

            Cache::store()->hset(CacheKey::serviceHashKey($this->job_id), $service_name, $container_config);
        }
    }
}
