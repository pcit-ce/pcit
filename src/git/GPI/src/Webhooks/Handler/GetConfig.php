<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler;

use App\GetAccessToken;
use App\Repo;
use PCIT\PCIT;
use Symfony\Component\Yaml\Yaml;

/**
 * 从 git 仓库获取 PCIT 配置文件 .pcit.yml.
 */
class GetConfig
{
    private $rid;

    private $commit_id;

    private $git_type;

    private $repo_full_name;

    public function __construct(int $rid, string $commit_id, $git_type = 'github')
    {
        $this->rid = $rid;
        $this->commit_id = $commit_id;
        $this->git_type = $git_type;
        $this->repo_full_name = Repo::getRepoFullName($rid, $git_type);
    }

    public function downloadConfig(array $configName = ['.pcit.yml'])
    {
        $commit_id = $this->commit_id;
        $git_type = $this->git_type;
        $repo_full_name = $this->repo_full_name;

        if ('github' === $git_type) {
            $access_token = GetAccessToken::getGitHubAppAccessToken(null, $repo_full_name);
        } else {
            $access_token = GetAccessToken::byRepoFullName($repo_full_name, null, $git_type);
        }

        foreach ($configName as $file_name) {
            try {
                return (new PCIT([], $git_type, $access_token))->repo_contents->getContents(
                $repo_full_name, $file_name, $commit_id);
            } catch (\Throwable $e) {
                continue;
            }

            \Log::info("$git_type $repo_full_name $commit_id not include ".$file_name);
        }

        return [];
    }

    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function handle()
    {
        $rid = $this->rid;
        $commit_id = $this->commit_id;
        $git_type = $this->git_type;
        $repo_full_name = $this->repo_full_name;

        \Log::info('Parse repo id', [
            'git_type' => $git_type, 'rid' => $rid, 'repo_full_name' => $repo_full_name, ]);

        $yaml_file_content = $this->downloadConfig([
                '.pcit.yml',
                '.pcit.yaml',
                ]);

        if ($yaml_file_content === []) {
            return [];
        }

        // yaml_parse($yaml_file_content)
        $config = Yaml::parse($yaml_file_content);

        if (!$config) {
            \Log::info("$git_type $repo_full_name $commit_id .pcit.yml parse error", []);

            return [];
        }

        return $config;
    }
}
