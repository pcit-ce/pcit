<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon;

use App\Build;
use App\Events\Build as BuildEvent;
use App\Events\CheckAdmin;
use Error;
use Exception;
use PCIT\Framework\Support\DB;
use PCIT\Framework\Support\HttpClient;
use PCIT\Framework\Support\StringSupport;
use PCIT\Framework\Support\Subject;
use PCIT\Support\CI;
use TencentAI\TencentAI;

/**
 * Run Server node, not need docker.
 *
 * 1. 处理 webhooks 数据，存入数据库
 * 2. 从数据库中取出数据，生成 jobs
 */
class Server extends Kernel
{
    private $git_type;

    private $subject;

    public function __construct()
    {
        $this->subject = new Subject();

        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        try {
            // 从 Webhooks 缓存中拿出数据，存入数据库
            $this->handleWebhooks();

            // 从数据库中取出数据，生成 jobs
            try {
                $this->handleBuild();
            } catch (\Throwable $e) {
                \Log::debug($e->__toString(), []);
            }
        } catch (\Throwable $e) {
            \Log::error($e->__toString(), []);
        } finally {
            $this->closeResource();
        }
    }

    /**
     * @throws \Exception
     */
    public function handleBuild(): void
    {
        // get build info
        $buildData = (new BuildEvent())->handle();

        try {
            $this->subject
                // check ci root
                ->register(new CheckAdmin($buildData))
                ->handle();
        } catch (\Throwable $e) {
            // 出现异常，直接将 build 状态改为 取消
            Build::updateBuildStatus(
                $buildData->build_key_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);

            return;
        }

        Build::updateBuildStatus(
            $buildData->build_key_id, CI::GITHUB_CHECK_SUITE_STATUS_QUEUED);

        try {
            // 处理 build 生成 jobs
            $this->pcit->runner->handle($buildData);
        } catch (\Throwable $e) {
            \Log::emergency($e->__toString(), ['message' => $e->getMessage(), 'code' => $e->getCode()]);
        }
    }

    /**
     * 关闭资源.
     */
    public function closeResource(): void
    {
        DB::close();
        \Cache::close();
        HttpClient::close();
        \Log::close();
        TencentAI::close();
    }

    /**
     * 从缓存中拿出 webhooks 数据，存入数据库中.
     *
     * @throws \Exception
     */
    private function handleWebhooks(): void
    {
        \Log::debug('start handle webhooks');

        $webhooks = $this->pcit->webhooks;

        while (true) {
            \Log::debug('pop webhooks redis list ...');

            $json_raw = $webhooks->getCache();

            \Log::debug('pop webhooks redis list success');

            if (!$json_raw) {
                \Log::debug('Redis list empty, quit');

                return;
            }

            list($git_type, $event_type, $json) = json_decode($json_raw, true);

            $provider = StringSupport::camelize($git_type);
            $class = 'PCIT\Provider\\'.ucfirst($provider).'\\WebhooksHandler';

            if (class_exists($class)) {
                (new $class())->handle($json);

                \Log::info("$provider handle success", []);

                return;
            }

            $this->git_type = $git_type;

            $webhooksHandler = new \PCIT\GPI\Webhooks\Handler\Kernel();

            try {
                $webhooksHandler->$event_type($json, $git_type);
                \Log::info('[ '.$event_type.' ] webhooks handle success', compact('git_type'));
            } catch (Error | Exception $e) {
                \Log::error('[ '.$event_type.' ] webhooks handle error', [$e->__toString()]);
                $webhooks->pushErrorCache($json_raw);
            }
        }
    }
}
