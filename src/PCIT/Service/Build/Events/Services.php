<?php

declare(strict_types=1);

namespace PCIT\Service\Build\Events;

use App\Job;
use Exception;
use PCIT\PCIT;
use PCIT\Service\Build\Parse;
use PCIT\Support\Cache;

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

        Job::updateEnv($this->job_id, json_encode($this->matrix_config));

        Cache::store()->lPush((string) $this->job_id.'_services', 'end');

        foreach ($this->service as $service_name => $array) {
            $image = $array->image;
            $env = $array->environment ?? null;
            $entrypoint = $array->entrypoint ?? null;
            $commands = $array->commands ?? $array->command ?? null;

            $image = Parse::image($image, $this->matrix_config);

            $docker_container = (new PCIT())->docker->container;

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

            Cache::store()->hset('pcit/'.$this->job_id.'/services', $service_name, $container_config);
        }
    }
}
