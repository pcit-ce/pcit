<?php

declare(strict_types=1);

namespace PCIT\Runner\Events;

use PCIT\PCIT;
use PCIT\Runner\Events\Handler\EnvHandler;
use PCIT\Runner\Events\Handler\TextHandler;
use PCIT\Runner\JobGenerator;
use PCIT\Support\CacheKey;

class Services
{
    private $job_id;

    private $service;

    private $matrix_config;

    private $jobGenerator;

    /**
     * @param null|array $matrix_config ['k'=>'v']
     * @param mixed      $service
     */
    public function __construct($service, int $job_id, JobGenerator $jobGenerator, ?array $matrix_config)
    {
        $this->service = $service;
        $this->job_id = $job_id;
        $this->jobGenerator = $jobGenerator;
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

        $envHandler = new EnvHandler();

        foreach ($this->service as $service_name => $serviceContent) {
            /**
             * @var \PCIT\Runner\Agent\Interfaces\ServiceInterface
             */
            $class = 'PCIT\Runner\Agent\Docker\Service\\'.ucfirst($service_name).'Service';

            $image = null;
            $env = [];
            $entrypoint = null;
            $command = null;

            if (class_exists($class)) {
                list(
                'image' => $image,
                'env' => $env,
                'entrypoint' => $entrypoint,
                'command' => $command
                ) = $class::handle();
            }

            if (\is_object($serviceContent)) {
                $image = $serviceContent->image ?? $image ?? $service_name;
                $env = $serviceContent->env ?? $env ?? [];
                $entrypoint = $serviceContent->entrypoint ?? $entrypoint ?? null;
                $command = $serviceContent->command ?? $command ?? null;
            }

            $system_env = array_merge(
                $this->jobGenerator->system_env,
                $this->jobGenerator->system_job_env,
                $envHandler->obj2array($this->matrix_config),
            );

            $image = (new TextHandler())->handle($image ?? $service_name, $system_env);
            $env = $envHandler->handle($env ?? [], $system_env);

            /**
             * @var \Docker\Container\Client
             */
            $docker_container = app(PCIT::class)->docker->container;

            $entrypoint = \is_string($entrypoint ?? null) ? [$entrypoint ?? null] : $entrypoint ?? null;
            $command = \is_string($command ?? null) ? [$command ?? null] : $command ?? null;

            $container_config = $docker_container
                ->setEnv($env)
                ->setEntrypoint($entrypoint ?? null)
                ->setLabels([
                    'com.khs1994.ci.service' => (string) $this->job_id,
                    'com.khs1994.ci.service.name' => $service_name,
                    'com.khs1994.ci' => (string) $this->job_id,
                ])
                ->setImage($image)
                ->setCmd($command ?? null)
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

            \Log::info('🌐Handle service '.$service_name, json_decode($container_config, true));

            \Cache::hset(CacheKey::serviceHashKey($this->job_id), $service_name, $container_config);
        }
    }
}
