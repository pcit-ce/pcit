<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\Events\GetBuild;
use App\Job;
use App\Notifications\GitHubChecksConclusion\Cancelled;
use PCIT\Framework\Attributes\Route;
use PCIT\Framework\Http\Request;
use PCIT\Framework\Support\DB;
use PCIT\Log\LogHandler;
use PCIT\Support\CI;

class JobController
{
    /**
     * @param $build_key_id
     *
     * @return array
     */
    #[Route('get', 'api/jobs')]
    public function list($build_key_id)
    {
        return Job::getByBuildKeyID((int) $build_key_id);
    }

    /**
     * @param $job_id
     *
     * @return array|int
     */
    #[Route('get', 'api/job/{job.id}')]

    // #[Query(["sse"])]
    public function find(Request $request, $job_id)
    {
        $job = Job::find((int) $job_id);
        $sse = $request->get('sse');

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
     * @param $job_id
     */
    #[Route('post', 'api/job/{job.id}/cancel')]
    public function cancel($job_id): void
    {
        $job_id = (int) $job_id;

        $this->handleCancel($job_id);

        if (\function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        $this->updateBuildStatus($job_id);
        $build_key_id = Job::getBuildKeyId($job_id);
        Build::updateFinishedAt($build_key_id, false, true);
    }

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
     * @param $job_id
     */
    #[Route('post', 'api/job/{job.id}/restart')]
    public function restart($job_id): void
    {
        $job_id = (int) $job_id;
        $buildId = Job::getBuildKeyId($job_id);

        $build = (new GetBuild())->handle($buildId);

        if (!$build) {
            return;
        }

        \PCIT::runner_job_generator()->handle($build, (int) $job_id);

        // 删除 s3 中的log
        (new LogController())->deleteStoreInS3($job_id);
        Job::updateBuildStatus($job_id, 'queued');

        $this->updateBuildStatus($job_id);
        Build::updateFinishedAt($buildId, true);
        Job::updateFinishedAt($job_id, 0);
        Job::updateStartAt($job_id, 0);
        Job::deleteLog($job_id);
    }

    /**
     * 更新 job 的状态，同时更新 build 的状态
     */
    private function updateBuildStatus(int $job_id): void
    {
        $build_key_id = Job::getBuildKeyId($job_id);

        $status = Job::getBuildStatusByBuildKeyId($build_key_id);

        Build::updateBuildStatus($build_key_id, $status);
    }
}
