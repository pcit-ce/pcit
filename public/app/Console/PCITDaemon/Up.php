<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon;

use App\Console\Webhooks\AliYunRegistry;
use App\Console\Webhooks\GitHub\Check;
use App\Console\Webhooks\GitHub\Delete;
use App\Console\Webhooks\GitHub\Installation;
use App\Console\Webhooks\GitHub\Issues;
use App\Console\Webhooks\GitHub\Ping;
use App\Console\Webhooks\GitHub\PullRequest;
use App\Console\Webhooks\GitHub\Push;
use Error;
use Exception;
use PCIT\PCIT;
use PCIT\Support\Cache;
use PCIT\Support\DB;
use PCIT\Support\HTTP;
use PCIT\Support\Log;
use TencentAI\TencentAI;

class Up
{
    private $git_type;

    /**
     * @throws Exception
     */
    public function up(): void
    {
        try {
            // 从 Webhooks 缓存中拿出数据，进行处理
            $this->webhooks();

            // Docker 构建队列
            try {
                (new Build())->build();
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
     * 从缓存中拿出 webhooks 数据，并进行处理.
     *
     * @throws Exception
     */
    private function webhooks(): void
    {
        Log::debug(__FILE__, __LINE__, 'start handle webhooks');

        $webhooks = (new PCIT())->webhooks;

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
     * @param string $content
     *
     * @throws Exception
     */
    public function ping(string $content): void
    {
        Ping::handle($content);
    }

    /**
     * push.
     *
     * 1. 首次推送到新分支，head_commit 为空
     *
     * @param string $json_content
     *
     * @throws Exception
     */
    public function push(string $json_content): void
    {
        Push::handle($json_content);
    }

    public function status(string $content)
    {
        return 200;
    }

    /**
     *  "assigned", "unassigned",
     *  "labeled",  "unlabeled",
     *  "opened",   "edited", "closed" or "reopened"
     *  "milestoned", "demilestoned".
     *
     * @param string $json_content
     *
     * @throws Exception
     */
    public function issues(string $json_content): void
    {
        Issues::handle($json_content);
    }

    /**
     * "created", "edited", or "deleted".
     *
     * @param string $json_content
     *
     * @throws Exception
     */
    public function issue_comment(string $json_content): void
    {
        Issues::comment($json_content);
    }

    /**
     * Action.
     *
     * "assigned", "unassigned", "review_requested", "review_request_removed",
     * "labeled", "unlabeled", "opened", "synchronize", "edited", "closed", or "reopened"
     *
     * @param string $json_content
     *
     * @return array|void
     *
     * @throws Exception
     */
    public function pull_request(string $json_content)
    {
        PullRequest::handle($json_content);
    }

    /**
     * Do Nothing.
     */
    public function watch()
    {
        return 200;
    }

    /**
     * Do Nothing.
     */
    public function fork()
    {
        return 200;
    }

    /**
     * Do nothing.
     */
    public function release()
    {
        return 200;
    }

    /**
     * Create "repository", "branch", or "tag".
     *
     * @param string $content
     *
     * @return int
     *
     * @throws Exception
     */
    public function create(string $content)
    {
        return 200;
    }

    /**
     * Delete tag or branch.
     *
     * @param string $json_content
     *
     * @throws Exception
     */
    public function delete(string $json_content): void
    {
        Delete::handle($json_content);
    }

    /**
     * action `added` `deleted` `edited` `removed`.
     *
     * @param string $content
     *
     * @throws Exception
     */
    public function member(string $content): void
    {
    }

    /**
     * @param string $content
     */
    public function team_add(string $content): void
    {
        $obj = json_decode($content);

        $repository = $obj->repository;

        $rid = $repository->id;
        $username = $repository->owner->name;

        $installation_id = $obj->installation->id ?? null;
    }

    /**
     * Any time a GitHub App is installed or uninstalled.
     *
     * action:
     *
     * created 用户点击安装按钮
     *
     * deleted 用户卸载了 GitHub Apps
     *
     * @see
     *
     * @param string $json_content
     *
     * @throws Exception
     */
    public function installation(string $json_content): void
    {
        Installation::handle($json_content);
    }

    /**
     * Any time a repository is added or removed from an installation.
     *
     * action:
     *
     * added 用户增加仓库
     *
     * removed 移除仓库
     *
     * @param string $json_content
     *
     * @throws Exception
     */
    public function installation_repositories(string $json_content): void
    {
        Installation::repositories($json_content);
    }

    /**
     * @deprecated
     */
    public function integration_installation(): void
    {
    }

    /**
     * @deprecated
     */
    public function integration_installation_repositories(): void
    {
    }

    /**
     * Action.
     *
     * completed
     *
     * requested 用户推送分支，github post webhooks
     *
     * rerequested 用户点击了重新运行按钮
     *
     *
     * @see https://developer.github.com/v3/activity/events/types/#checksuiteevent
     *
     * @param string $json_content
     *
     * @throws Exception
     */
    public function check_suite(string $json_content): void
    {
        Check::suite($json_content);
    }

    /**
     * Action.
     *
     * created updated rerequested
     *
     * @see https://developer.github.com/v3/activity/events/types/#checkrunevent
     *
     * @param string $content
     *
     * @throws Exception
     */
    public function check_run(string $json_content): void
    {
        Check::run($json_content);
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
