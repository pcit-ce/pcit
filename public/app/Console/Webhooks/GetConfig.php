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
    /**
     * @param int    $rid       repo id
     * @param string $commit_id commit id or url(only test)
     * @param string $git_type
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function handle(int $rid, string $commit_id, $git_type = 'github')
    {
        $repo_full_name = Repo::getRepoFullName($git_type, $rid);

        Log::debug(__FILE__, __LINE__, "${git_type} ${rid} is $repo_full_name", [], Log::INFO);

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
