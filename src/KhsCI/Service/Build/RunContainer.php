<?php

declare(strict_types=1);

namespace KhsCI\Service\Build;

use App\Job;
use Docker\Container\Client as Container;
use Docker\Network\Client as Network;
use KhsCI\CIException;
use KhsCI\KhsCI;
use KhsCI\Support\Cache;
use KhsCI\Support\CI;
use KhsCI\Support\Log;

class RunContainer
{
    /**
     * @var Container
     */
    private $docker_container;

    /**
     * @var Network
     */
    private $docker_network;

    /**
     * @param int $build_key_id
     *
     * @throws CIException
     * @throws \Exception
     */
    public function run(int $build_key_id): void
    {
        $docker = (new KhsCI())->docker;

        $this->docker_container = $docker->container;
        $this->docker_network = $docker->network;

        $jobs = Job::getByBuildKeyID($build_key_id);

        foreach ($jobs as $job_id) {
            self::job((int) $job_id);
        }

        // 所有 job 执行完毕
        throw new CIException(CI::BUILD_STATUS_PASSED);
    }

    /**
     * @param int $job_id
     *
     * @throws \Exception
     */
    private function job(int $job_id): void
    {
        LogClient::drop($job_id);

        $this->runService($job_id);

        $this->docker_network->create((string) $job_id);

        Log::debug(__FILE__, __LINE__, 'Create Network '.$job_id, [], Log::EMERGENCY);

        while (1) {
            $container_config = Cache::connect()->rPop((string) $job_id);

            if (!$container_config) {
                // 某一 job 执行完毕
                // success
                // changed
                // 清理
                Cleanup::systemDelete((string) $job_id, true);
            }

            $labels = (json_decode($container_config, true))['Labels'];

            $no_status = $labels['com.khs1994.ci.pipeline.status.no_status'] ?? false;

            $is_git = $labels['com.khs1994.ci.git'] ?? false;

            if (!$no_status or !$is_git) {
                Cache::connect()->lPush($job_id.'_after', $container_config);
                continue;
            }

            $container_id = $this->docker_container
                ->setCreateJson($container_config)
                ->create(false)
                ->start(null);

            LogClient::get($job_id, $this->docker_container, $container_id);
        }
    }

    /**
     * @param $job_id
     *
     * @throws \Exception
     */
    private function runService($job_id): void
    {
        while (1) {
            $container_config = Cache::connect()->rPop((string) $job_id.'_services');

            if (!$container_config) {
                break;
            }

            $this->docker_container
                ->setCreateJson($container_config)
                ->create(false)
                ->start(null);

            Log::debug(__FILE__, __LINE__, 'Run Services '.$job_id, [], LOG::EMERGENCY);
        }
    }
}
