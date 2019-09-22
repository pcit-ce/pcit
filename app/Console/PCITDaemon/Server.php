<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon;

use App\Build;
use App\Events\Build as BuildEvent;
use App\Events\CheckAdmin;
use App\Console\Webhooks\AliYunRegistry;
use Error;
use Exception;
use PCIT\Support\Cache;
use PCIT\Support\CI;
use PCIT\Support\DB;
use PCIT\Support\HTTP;
use PCIT\Support\Log;
use PCIT\Support\Subject;
use TencentAI\TencentAI;

/**
 * Run Server node without docker.
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
     * @throws Exception
     */
    public function handle(): void
    {
        try {
            // 从 Webhooks 缓存中拿出数据，存入数据库
            $this->webhooks();

            // 从数据库中取出数据，生成 jobs
            try {
                $this->build();
            } catch (\Throwable $e) {
                Log::debug(__FILE__, __LINE__, $e->__toString(), []);
            }
        } catch (\Throwable $e) {
            Log::debug(__FILE__, __LINE__, $e->__toString(), [], LOG::ERROR);
        } finally {
            $this->closeResource();
        }
    }

    /**
     * @throws Exception
     */
    public function build(): void
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
            // 处理 build
            $this->pcit->build->handle($buildData);
        } catch (\Throwable $e) {
            Log::debug(__FILE__, __LINE__, $e->__toString(), [
                'message' => $e->getMessage(), 'code' => $e->getCode(), ], Log::EMERGENCY);
        }
    }

    /**
     * 关闭资源.
     */
    public function closeResource(): void
    {
        DB::close();
        Cache::close();
        HTTP::close();
        Log::close();
        TencentAI::close();
    }

    /**
     * 外部调用服务
     *
     * @throws Exception
     */
    public static function runWebhooks(): void
    {
        (new self())->webhooks();
    }

    /**
     * 从缓存中拿出 webhooks 数据，存入数据库中.
     *
     * @throws Exception
     */
    private function webhooks(): void
    {
        Log::debug(__FILE__, __LINE__, 'start handle webhooks');

        $webhooks = $this->pcit->webhooks;

        while (true) {
            Log::debug(__FILE__, __LINE__, 'pop webhooks redis list ...');

            $json_raw = $webhooks->getCache();

            Log::debug(__FILE__, __LINE__, 'pop webhooks redis list success');

            if (!$json_raw) {
                Log::debug(__FILE__, __LINE__, 'Redis list empty, quit');

                return;
            }

            list($git_type, $event_type, $json) = json_decode($json_raw, true);

            if ('aliyun_docker_registry' === $git_type) {
                $this->aliyunDockerRegistry($json);

                Log::debug(__FILE__, __LINE__, 'Aliyun Docker Registry handle success', [], Log::INFO);

                return;
            }

            $this->git_type = $git_type;

            try {
                $this->$event_type($json);
                Log::debug(__FILE__, __LINE__, $event_type.' webhooks handle success', [], Log::INFO);
            } catch (Error | Exception $e) {
                Log::debug(__FILE__, __LINE__, $event_type.' webhooks handle error', [$e->__toString()], Log::ERROR);
                $webhooks->pushErrorCache($json_raw);
            }
        }
    }

    /**
     * @param string $json_content
     *
     * @throws Exception
     */
    private function aliyunDockerRegistry(string $json_content): void
    {
        AliYunRegistry::handle($json_content);
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->$name(...$arguments);
        }

        return 0;
    }
}
