<?php

declare(strict_types=1);

namespace PCIT\Runner\Agent\Docker;

use Exception;
use PCIT\Framework\Support\Date;
use PCIT\Log\Handler\DebugHandler;
use PCIT\Log\Handler\EnvHandler as LogEnvHandler;
use PCIT\Log\Handler\ErrorHandler;
use PCIT\Log\Handler\MaskHandler;
use PCIT\Log\Handler\OutputHandler;
use PCIT\Log\Handler\WarningHandler;
use PCIT\PCIT;
use PCIT\Runner\Events\Handler\EnvHandler;
use PCIT\Support\CacheKey;
use PCIT\Support\CI;

class Log
{
    private $container_id;

    private $job_id;

    private $step;

    private $cache;

    public function __construct(int $job_id, string $container_id, string $step = null)
    {
        $this->job_id = $job_id;
        $this->container_id = $container_id;
        $this->step = $step;
        $this->cache = \Cache::store();
    }

    /**
     * @param $job_id
     *
     * @throws \Exception
     */
    public static function drop(int $job_id): void
    {
        \Log::emergency('🗑Drop prev job '.$job_id.' logs', []);

        \Cache::del(CacheKey::logHashKey($job_id));
    }

    /**
     * @throws \Exception
     *
     * @return array<array>
     */
    public function handle(array $mask_value_array = []): array
    {
        $i = -1;

        $startedAt = null;
        $finishedAt = null;
        $until_time = 0;

        /**
         * @var \Docker\Container\Client
         */
        $docker_container = app(PCIT::class)->docker->container;

        while (1) {
            // 循环遍历日志
            $i = $i + 1;

            $container_status_obj = json_decode($docker_container->inspect($this->container_id))->State;
            $status = $container_status_obj->Status;
            $startedAt = Date::parse($container_status_obj->StartedAt);

            if ('running' === $status) {
                // 处于运行状态
                if (0 === $i) {
                    $since_time = $startedAt;
                    $until_time = $startedAt;
                } else {
                    $since_time = $until_time;
                    $until_time = $until_time + 1;
                }

                $container_log = $docker_container->logs(
                    $this->container_id,
                    false,
                    true,
                    true,
                    $since_time,
                    $until_time,
                    true
                );

                // echo $container_log;

                sleep(2);

                continue;
            }
            // 容器停止，获取日志
            $container_log = $docker_container->logs(
                $this->container_id,
                false,
                true,
                true,
                0,
                0,
                true
            );

            if (!$container_log) {
                $container_log = '12345678 log not found!';
            }

            // 去掉非法字符
            $container_log = $this->fmt($container_log);

            // env
            [$container_log,$env] = (new LogEnvHandler())->handle($container_log, 31);

            $env = (new EnvHandler())->obj2array($env);

            // path

            // output
            [$container_log,$output] = (new OutputHandler())->handle($container_log, 31);

            // debug
            [$container_log,$debug_context] = (new DebugHandler())->handle($container_log, 31);

            // warning
            [$container_log,$warning_context] = (new WarningHandler())->handle($container_log, 31);

            // error
            [$container_log,$error_context] = (new ErrorHandler())->handle($container_log, 31);

            // mask
            [$container_log,$hide_value ] = (new MaskHandler())
                ->handle($container_log, 31, $mask_value_array);

            $exitCode = $container_status_obj->ExitCode;
            $finishedAt = $container_status_obj->FinishedAt;

            // store log
            $this->storeLog($container_log, $exitCode, $finishedAt);

            /**
             * 2018-05-01T05:16:37.6722812Z
             * 0001-01-01T00:00:00Z.
             */
            $startedAt = $container_status_obj->StartedAt;

            if (0 !== $exitCode) {
                \Log::error("🛑Container $this->container_id ExitCode is $exitCode, not 0", []);

                throw new Exception(CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE);
            }

            break;
        }

        return [
            'start' => $startedAt,
            'stop' => $finishedAt,
            'env' => $env ?? [],
            'mask' => $hide_value ?? [],
            'context' => [
                'error' => $error_context ?? [],
                'debug' => $debug_context ?? [],
                'warning' => $warning_context ?? [],
            ],
            'output' => $output ?? [],
            // 'path' => $path ?? []
        ];
    }

    public function storeLog(string $container_log, int $exitCode, string $finishedAt): void
    {
        $cache = $this->cache;

        $cache->hset(
            CacheKey::logHashKey($this->job_id),
            $this->step,
            $container_log."\n".substr($finishedAt, 0, 20).'000000000Z'.' ::exit-code::'.$exitCode
        );
    }

    public function fmt(string $log): string
    {
        $log_line_array = explode("\n", $log) ?: [];

        $log = [];

        foreach ($log_line_array as $line) {
            $line = substr($line, 8);

            $log[] = $line;
        }

        $log = implode("\n", $log);

        return iconv('utf-8', 'utf-8//IGNORE', trim($log));
    }
}
