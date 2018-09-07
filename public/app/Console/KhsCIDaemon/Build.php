<?php

declare(strict_types=1);

namespace App\Console\KhsCIDaemon;

use App\Build as BuildEloquent;
use App\Console\Events\Build as BuildEvent;
use App\Console\Events\CheckAdmin;
use App\Console\Events\Subject;
use App\Console\Events\UpdateBuildStatus;
use App\Job;
use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\CI;
use KhsCI\Support\Log;

class Build
{
    /**
     * @throws Exception
     */
    public function build(): void
    {
        // get build info
        $buildData = (new BuildEvent())->handle();

        // 观察者模式
        $subject = new Subject();

        try {
            $subject
                // check ci root
                ->register(new CheckAdmin($buildData))
                ->handle();
        } catch (\Throwable $e) {
            // 出现异常，直接将 build 状态改为 取消

            BuildEloquent::updateBuildStatus(
                $buildData->build_key_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);

            return;
        }

        BuildEloquent::updateBuildStatus(
            $buildData->build_key_id, CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS);

        try {
            // exec build
            (new KhsCI())->build->handle($buildData);

            $job_ids = Job::getByBuildKeyID($buildData->build_key_id);

            foreach ($job_ids as $job_id) {
                $job_id = $job_id['id'];
                Log::debug(__FILE__, __LINE__,
                    'Handle build jobs', ['job_id' => $job_id], Log::EMERGENCY);

                $subject
                    // update build status in progress
                    ->register(
                        new UpdateBuildStatus((int) $job_id, $buildData->config, CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS)
                    )
                    ->handle();

                (new KhsCI())->build_agent->handle((int) $buildData->build_key_id);
            }
        } catch (\Throwable $e) {
            Log::debug(__FILE__, __LINE__, $e->__toString(), [
                'message' => $e->getMessage(), 'code' => $e->getCode(), ], Log::EMERGENCY);
        }
    }
}
