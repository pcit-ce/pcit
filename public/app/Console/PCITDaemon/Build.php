<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon;

use App\Build as BuildEloquent;
use App\Console\Events\Build as BuildEvent;
use App\Console\Events\CheckAdmin;
use App\Console\Events\Subject;
use Exception;
use PCIT\PCIT;
use PCIT\Support\CI;
use PCIT\Support\Log;

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
            (new PCIT())->build->handle($buildData);

            // agent run job
            (new Agent())->handle($buildData->build_key_id, $buildData->config);
        } catch (\Throwable $e) {
            Log::debug(__FILE__, __LINE__, $e->__toString(), [
                'message' => $e->getMessage(), 'code' => $e->getCode(), ], Log::EMERGENCY);
        }
    }
}
