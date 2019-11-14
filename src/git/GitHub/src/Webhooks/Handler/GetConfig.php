<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use App\Repo;
use Exception;
use PCIT\Framework\Support\HTTP;
use PCIT\Framework\Support\Log;
use PCIT\Support\Git;
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

        foreach ($configName as $file_name) {
            $url = Git::getRawUrl($git_type, $repo_full_name, $commit_id, $file_name);

            $yaml_file_content = HTTP::get($url, null, [], 20);

            if (404 !== Http::getCode()) {
                return $yaml_file_content;
            }

            Log::debug(__FILE__, __LINE__, "$repo_full_name $commit_id not include ".$file_name, [], Log::INFO);
        }

        return [];
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function handle()
    {
        $rid = $this->rid;
        $commit_id = $this->commit_id;
        $git_type = $this->git_type;
        $repo_full_name = $this->repo_full_name;

        Log::debug(__FILE__, __LINE__, 'Parse repo id', [
            'git_type' => $git_type, 'rid' => $rid, 'repo_full_name' => $repo_full_name, ],
            Log::INFO);

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
            Log::debug(__FILE__, __LINE__, "$repo_full_name $commit_id .pcit.yml parse error", [], Log::INFO);

            return [];
        }

        return $config;
    }
}
