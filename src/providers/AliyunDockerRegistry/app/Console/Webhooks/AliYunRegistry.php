<?php

declare(strict_types=1);

namespace App\Console\Webhooks;

use App\Build;
use App\Notifications\GitHubAppChecks;
use App\Repo;
use PCIT\Framework\Support\JSON;
use PCIT\Framework\Support\Log;
use PCIT\PCIT;
use PCIT\Support\CI;

/**
 * 处理 Aliyun 容器镜像服务的 webhooks.
 */
class AliYunRegistry
{
    /**
     * @param $json_content
     *
     * @throws \Exception
     */
    public static function handle($json_content): void
    {
        Log::debug(__FILE__, __LINE__, 'Receive Aliyun Docker Registry Webhooks', [], Log::INFO);

        $obj = json_decode($json_content);

        $aliyun_docker_registry_name = $obj->repository->repo_full_name;

        $aliyun_docker_registry_tagname = $obj->push_data->tag;

        $aliyun_docker_registry = [];

        require __DIR__.'/../../../config/config.php';

        if (\array_key_exists($aliyun_docker_registry_name, $aliyun_docker_registry)) {
            $git_repo_full_name = $aliyun_docker_registry["$aliyun_docker_registry_name"];

            $name = 'Aliyun Docker Registry Push '.$aliyun_docker_registry_name.':'.$aliyun_docker_registry_tagname;

            Log::debug(__FILE__, __LINE__, $name, [], Log::INFO);

            GitHubAppChecks::send(
                (int) Build::getCurrentBuildKeyId(
                    'github', (int) Repo::getRid(
                    'github', ...explode('/', (string) $git_repo_full_name)
                )
                ),
                $name,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                time(),
                time(),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS,
                'Aliyun Docker Registry Push',
                'Build Docker image Success',
                (new PCIT())->check_md->aliyunDockerRegistry(
                    'PHP',
                    PHP_OS,
                    JSON::beautiful($json_content)
                ),
                null,
                null,
                null,
                true
            );
        }

        return;
    }
}
