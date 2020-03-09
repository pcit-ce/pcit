<?php

declare(strict_types=1);

namespace PCIT\Runner\Events;

use Docker\Container\Client;
use PCIT\PCIT;
use PCIT\Runner\Parser\TextHandler as TextParser;
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
     * @throws \Exception
     */
    public function handle(): void
    {
        if (null === $this->service) {
            return;
        }

        foreach ($this->service as $service_name => $serviceContent) {
            list(
                'image' => $image,
                'env' => $env,
                'entrypoint' => $entrypoint,
                'commands' => $commands
                ) = ServiceDefault::handle($service_name);

            if (\is_array($serviceContent)) {
                $image = $serviceContent->image ?? $image;
                $env = $serviceContent->environment ?? $env;
                $entrypoint = $serviceContent->entrypoint ?? $entrypoint;
                $commands = $serviceContent->commands ?? $serviceContent->command ?? $commands;

                $image = (new TextParser())->handle($image, $this->matrix_config);
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
                        "pcit_$this->job_id" => [
                            'Aliases' => [
                                $service_name,
                            ],
                        ],
                    ],
                ])
                ->setCreateJson(null)
                ->getCreateJson();

            \Cache::store()->hset(CacheKey::serviceHashKey($this->job_id), $service_name, $container_config);
        }
    }
}
