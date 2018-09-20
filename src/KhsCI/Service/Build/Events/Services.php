<?php

declare(strict_types=1);

namespace KhsCI\Service\Build\Events;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Service\Build\Parse;
use KhsCI\Support\Cache;

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
            $image = $array->image;
            $env = $array->environment ?? null;
            $entrypoint = $array->entrypoint ?? null;
            $command = $array->command ?? null;

            $image = Parse::image($image, $this->matrix_config);

            $docker_container = (new KhsCI())->docker->container;

            $container_config = $docker_container
                ->setEnv($env)
                ->setEntrypoint($entrypoint)
                ->setLabels([
                    'com.khs1994.ci.service' => (string) $this->job_id,
                    'com.khs1994.ci.service.name' => $service_name,
                    'com.khs1994.ci' => (string) $this->job_id,
                ])
                ->setImage($image)
                ->setCmd($command)
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

            Cache::store()->lPush((string) $this->job_id.'_services', $container_config);
        }
    }
}
