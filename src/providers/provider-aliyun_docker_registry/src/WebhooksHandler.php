<?php

declare(strict_types=1);

namespace PCIT\Provider\AliyunDockerRegistry;

use App\Build;
use App\Job;
use App\Notifications\GitHubAppChecks;
use App\Repo;
use PCIT\Framework\Support\JSON;
use PCIT\Provider\Interfaces\WebhooksHandlerInterface;
use PCIT\Support\CI;

/**
 * PCITD 处理 Aliyun 容器镜像服务的 webhooks.
 */
class WebhooksHandler implements WebhooksHandlerInterface
{
    public function handle(string $webhooks_content): void
    {
        \Log::info('Handle Aliyun Docker Registry Webhooks', []);

        $obj = json_decode($webhooks_content);

        $aliyun_docker_registry_name = $obj->repository->repo_full_name;

        $aliyun_docker_registry_tagname = $obj->push_data->tag;

        $aliyun_docker_registry = config('aliyun_docker_registry') ?? [];

        if (\array_key_exists($aliyun_docker_registry_name, $aliyun_docker_registry)) {
            $git_repo_full_name = $aliyun_docker_registry["$aliyun_docker_registry_name"];

            $name = 'Aliyun Docker Registry Push '.$aliyun_docker_registry_name.':'.$aliyun_docker_registry_tagname;

            \Log::info($name, []);

            // 发送到最新的一次 commit

            $build_key_id = (int) Build::getCurrentBuildKeyId(
                (int) Repo::getRid(
                    ...explode('/', (string) $git_repo_full_name)
                )
            );

            $job_key_id = (int) Job::getJobIDByBuildKeyID($build_key_id)[0];

            GitHubAppChecks::send(
                $job_key_id,
                $name,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                time(),
                time(),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS,
                'Aliyun Docker Registry Push',
                'Build Docker image Success',
                (new CheckRunText(
                    $build_key_id,
                    JSON::beautiful($webhooks_content),
                    'github'
                ))->markdown(),
                null,
                null,
                null,
                true
            );
        }
    }
}
