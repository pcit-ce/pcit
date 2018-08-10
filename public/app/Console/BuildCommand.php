<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Events\Build;
use App\Console\Events\CheckAdmin;
use App\Console\Events\Subject;
use App\Console\Events\UpdateBuildStatus;
use App\Job;
use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\CI;

class BuildCommand
{
    /**
     * @throws Exception
     */
    public function build(): void
    {
        // get build info
        $buildData = (new Build())->handle();

        $subject = new Subject();

        try {
            $subject
                // check ci root
                ->register(new CheckAdmin($buildData))
                ->handle();
        } catch (\Throwable $e) {
            // 出现异常，直接将 build 状态改为 取消

            \App\Build::updateBuildStatus(
                $buildData->build_key_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);

            return;
        }

        \App\Build::updateBuildStatus(
            $buildData->build_key_id, CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS);

        // exec build
        (new KhsCI())->build->handle($buildData);

        $job_ids = Job::getByBuildKeyID($buildData->build_key_id);

        foreach ($job_ids as $job_id) {
            $subject
                // update build status in progress
                ->register(new UpdateBuildStatus(
                    $job_id, $buildData->config, CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS));
        }
    }
}
