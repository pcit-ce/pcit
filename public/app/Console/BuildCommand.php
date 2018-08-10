<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\BuildFunction\Build;
use App\Console\BuildFunction\CheckAdmin;
use App\Console\BuildFunction\LogHandle;
use App\Console\BuildFunction\Subject;
use App\Console\BuildFunction\UpdateBuildStatus;
use App\Notifications\WeChatTemplate;
use Exception;
use KhsCI\CIException;
use KhsCI\KhsCI;
use KhsCI\Support\Cache;
use KhsCI\Support\CI;
use KhsCI\Support\Env;
use KhsCI\Support\Log;

class BuildCommand
{
    /**
     * @var KhsCI
     */
    private $khsci;

    public function __set($name, $value): void
    {
        $this->$name = $value;
    }

    /**
     * @throws Exception
     */
    public function build(): void
    {
        $this->khsci = new KhsCI();

        Log::debug(__FILE__, __LINE__, 'Docker connect ...');

        $this->khsci->docker->system->ping(1);

        Log::debug(__FILE__, __LINE__, 'Docker build Start ...');

        try {
            // get build info
            $buildData = (new Build())->handle();

            $build = $this->khsci->build;
            $build_cleanup = $this->khsci->build_cleanup;

            $subject = new Subject();

            // check ci root
            $subject
                ->register(new CheckAdmin($buildData))
                // update build status in progress
                ->register(new UpdateBuildStatus(
                    $buildData->build_key_id, CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS))
                ->handle();

            // clear build environment
            $build_cleanup->systemDelete(null, false, true);

            // exec build
            $build($buildData);
        } catch (CIException $e) {
            // 数据库不存在项目，跳出
            if (01404 === $e->getCode()) {
                $buildData->build_key_id = 01404;

                return;
            }

            // $e->getCode() is build key id.
            \app\Build::updateStopAt($buildData->build_key_id);

            // save build log
            $subject->register(new LogHandle($buildData))
                // update build status
                ->register(new UpdateBuildStatus($buildData, $e->getMessage()))
                ->handle();

            Log::debug(__FILE__, __LINE__, $e->__toString(), [], Log::INFO);

            // update build status
        } catch (\Throwable  $e) {
            Log::debug(__FILE__, __LINE__, $e->__toString(), [], Log::ERROR);
            $this->updateBuildStatus(CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);
            // 出现其他错误
        } finally {
            UpCommand::runWebhooks();

            // 数据库不存在项目，跳出
            if (01404 === $buildData->build_key_id) {
                Log::debug(__FILE__, __LINE__, 'Docker Build stop by BuildDB empty');

                return;
            }

            $buildData->build_key_id
            && $this->build_status
            && \App\Build::updateBuildStatus($this->build_key_id, $this->build_status);

            $build_cleanup->systemDelete($this->unique_id, true);

            // wechat
            Env::get('CI_WECHAT_TEMPLATE_ID', false) && $this->description &&
            $this->weChatTemplate($this->description);

            // mail pr skip
            CI::BUILD_EVENT_PR !== $buildData->event_type && $this->sendEMail();

            // check pr auto merge
            // $this->autoMerge();

            Log::connect()->emergency('====== '.$buildData->build_key_id.' Build Stopped Success ======');

            Cache::store()->set('khsci_up_status', 0);
        }
    }

    /**
     * @param Build  $build
     * @param string $info
     *
     * @throws Exception
     */
    private function weChatTemplate(Build $build, string $info): void
    {
        WeChatTemplate::send($build->build_key_id, $info);
    }

    public function __call($name, $arguments): void
    {
        if (method_exists($this, $name)) {
            $this->$name(...$arguments);
        }
    }
}
