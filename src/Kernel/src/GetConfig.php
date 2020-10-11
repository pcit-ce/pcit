<?php

declare(strict_types=1);

namespace PCIT;

use App\GetAccessToken;
use App\Repo;
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

    public function __construct(int $rid, string $commit_id, string $git_type = 'github')
    {
        $this->rid = $rid;
        $this->commit_id = $commit_id;
        $this->git_type = $git_type;
        $this->repo_full_name = Repo::getRepoFullName($rid, $git_type);
    }

    public function downloadConfig(array $configName = ['.pcit.yml']): string
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
                return \PCIT::git($git_type, $access_token)->repo_contents->getContents(
                    $repo_full_name,
                    $file_name,
                    $commit_id
                );
            } catch (\Throwable $e) {
                \Log::info("$git_type $repo_full_name $commit_id not include ".$file_name);

                continue;
            }
        }

        return '';
    }

    /**
     * |\Symfony\Component\Yaml\Exception\ParseException.
     */
    public function handle(): array
    {
        $rid = $this->rid;
        $commit_id = $this->commit_id;
        $git_type = $this->git_type;
        $repo_full_name = $this->repo_full_name;

        \Log::info('Get pcit config', compact(
            'git_type',
            'repo_full_name',
            'rid',
            'commit_id'
        ));

        $yaml_file_content = $this->downloadConfig([
            '.pcit.yml',
            '.pcit.yaml',
        ]);

        if ('' === $yaml_file_content) {
            return [];
        }

        // yaml_parse($yaml_file_content)
        $config = Yaml::parse($yaml_file_content);

        if (!$config) {
            \Log::info('.pcit.yml parse result is null', []);

            return [];
        }

        return $config;
    }
}
