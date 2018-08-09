<?php

declare(strict_types=1);

namespace App\Console\Webhooks;

use App\Repo;
use Exception;
use KhsCI\Support\Git;
use KhsCI\Support\HTTP;
use KhsCI\Support\Log;

class GetConfig
{
    private $rid;
    private $commit_id;
    private $git_type;

    public function __construct(int $rid, string $commit_id, $git_type = 'github')
    {
        $this->rid = $rid;
        $this->commit_id = $commit_id;
        $this->git_type = $git_type;
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
        $repo_full_name = Repo::getRepoFullName($rid, $git_type);

        Log::debug(__FILE__, __LINE__, 'Parse rid', [$git_type => [$rid => $repo_full_name]], Log::INFO);

        $url = Git::getRawUrl($git_type, $repo_full_name, $commit_id, '.khsci.yml');

        $yaml_file_content = HTTP::get($url);

        if (404 === Http::getCode()) {
            Log::debug(__FILE__, __LINE__, "$repo_full_name $commit_id not include .khsci.yml", [], Log::INFO);

            return [];
        }

        if (!$yaml_file_content) {
            Log::debug(__FILE__, __LINE__, "$repo_full_name $commit_id not include .khsci.yml", [], Log::INFO);

            return [];
        }

        $config = yaml_parse($yaml_file_content);

        if (!$config) {
            Log::debug(__FILE__, __LINE__, "$repo_full_name $commit_id .khsci.yml parse error", [], Log::INFO);

            return [];
        }

        return $config;
    }
}
