<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\Job;
use App\Notifications\GitHubChecksConclusion\Cancelled;
use PCIT\Framework\Support\DB;
use PCIT\Log\LogHandler;
use PCIT\PCIT;
use PCIT\Support\CI;

class JobController
{
    /**
     * @param $build_key_id
     *
     * @return array
     *
     * @throws \Exception
     */
    public function list($build_key_id)
    {
        return Job::getByBuildKeyID((int) $build_key_id);
    }

    /**
     * GET.
     *
     * /job/{job.id}
     *
     * @param $job_id
     *
     * @return array|int
     *
     * @throws \Exception
     */
    public function find($job_id)
    {
        $job = Job::find((int) $job_id);
        $sse = \Request::get('sse');

        if ($sse) {
            $logHandler = new LogHandler((int) $job_id);
            $steps = $logHandler->getSteps();

            header('X-Accel-Buffering: no');
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');

            while (1) {
                $build_log = [];
                $id = 1;

                foreach ($steps as $step) {
                    $build_log[$step] = $logHandler->handlePipeline($step);
                }

                $job['build_log'] = json_encode($build_log);

                $job_json = json_encode($job);

                // 获取状态
                if (!\in_array(Job::getBuildStatus((int) $job_id), [CI::GITHUB_CHECK_SUITE_STATUS_QUEUED])) {
                    // 已完成，从数据库获取 job 数据
                    $job = Job::find((int) $job_id);
                    $job_json = json_encode($job);
                    echo "id: $id\nevent: close\nretry: 1000\ndata: $job_json \n\n";
                    flush();

                    return;
                }

                echo "id: $id\nretry: 1000\ndata: $job_json \n\n";

                flush();

                sleep(5);
            }

            return;
        }

        // 获取状态
        $state = $job['state'];
        if (!\in_array($state, [CI::GITHUB_CHECK_SUITE_STATUS_QUEUED])) {
            return $job;
        }

        $build_log = [];
        $logHandler = new LogHandler((int) $job_id);
        $steps = $logHandler->getSteps();
        foreach ($steps as $step) {
            $build_log[$step] = $logHandler->handlePipeline($step);
        }

        $job['build_log'] = json_encode($build_log);

        return $job;
    }

    /**
     * POST.
     *
     * /job/{job.id}/cancel
     *
     * @param $job_id
     *
     * @throws \Exception
     */
    public function cancel($job_id): void
    {
        $job_id = (int) $job_id;

        $this->handleCancel($job_id);

        if (\function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        $this->updateBuildStatus((int) $job_id);
    }

    /**
     * @throws \Exception
     */
    public function handleCancel(int $job_id): void
    {
        DB::beginTransaction();
        Job::updateBuildStatus($job_id, 'cancelled');
        Job::updateFinishedAt($job_id, time());
        $config = Build::getConfig(Job::getBuildKeyId($job_id));
        DB::commit();

        (new Cancelled($job_id, $config, null))->handle();
    }

    /**
     * POST.
     *
     * /job/{job.id}/restart
     *
     * @param $job_id
     *
     * @throws \Exception
     */
    public function restart($job_id): void
    {
        $job_id = (int) $job_id;
        $buildId = Job::getBuildKeyId($job_id);

        $build = (new \App\Events\Build())->handle($buildId);

        /**
         * @var \PCIT\PCIT
         */
        $pcit = app(PCIT::class);
        $pcit->runner_job_generator->handle($build, (int) $job_id);

        // 删除 s3 中的log
        (new LogController())->deleteStoreInS3($job_id);
        Job::updateBuildStatus($job_id, 'queued');

        $this->updateBuildStatus($job_id);
        Job::updateFinishedAt($job_id, 0);
        Job::updateStartAt($job_id, 0);
        Job::deleteLog($job_id);
    }

    /**
     * 更新 job 的状态，同时更新 build 的状态
     *
     * @throws \Exception
     */
    private function updateBuildStatus(int $job_id): void
    {
        $build_key_id = Job::getBuildKeyId($job_id);

        $status = Job::getBuildStatusByBuildKeyId($build_key_id);

        Build::updateBuildStatus($build_key_id, $status);
    }
}
