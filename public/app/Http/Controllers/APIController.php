<?php

namespace App\Http\Controllers;


use KhsCI\Support\Env;

class APIController
{
    public function __invoke()
    {
        $ci_host = Env::get('CI_HOST').'/api';

        $array = [
            'user' => [
                $ci_host.'/user/{git_type}',
                'beta_feature' => [
                    "get" => $ci_host.'/user/{git_type}/{username}/beta_features',
                    "update" => $ci_host.'/user/{git_type}/{username}/beta_feature/{beta_feature_id}',
                    "delete" => $ci_host.'/user/{git_type}/{username}/beta_feature/{beta_feature_id}',
                ],
                "sync" => $ci_host.'/user/{git_type}/{username}/sync',
            ],
            'repo' => [
                "branch" => [
                    "list@get" => $ci_host.'/repo/{git_type}/{username}/{repo_name}/branches',
                    "find@get" => $ci_host.'/repo/{git_type}/{username}/{repo_name}/branch/{branch_name}',
                ],
                "env_vars" => [
                    "list" => $ci_host.'/repo/{git_type}/{username}/{repo_name}/env_vars',
                    "create@post" => $ci_host.'/repo/{git_type}/{username}/{repo_name}/env_vars',
                    'find' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/env_var/{env_var_id}',
                    'update@patch' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/env_var/{env_var_id}',
                    'delete' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/env_var/{env_var_id}'
                ],
                "settings" => [
                    "list" => $ci_host.'/repo/{git_type}/{username}/{repo_name}/settings',
                    'get' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/setting/{setting_name}',
                    "update@patch" => $ci_host.'/repo/{git_type}/{username}/{repo_name}/setting/{setting_name}',
                ],
                "requests" => [
                    'list' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/requests',
                    'create' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/requests',
                    'get' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/request/{requests_id}',
                ],
                "caches" => [
                    'list' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/caches',
                    'delete' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/caches',
                ],
                "crons" => [
                    'list' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/crons',
                    'find' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/cron/{cron_id}',
                    'delete' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/cron/{cron_id}',
                    'findByBranch' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/branch/{branch_name}/cron',
                    'createByBranch' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/branch/{branch_name}/cron',
                ],
                "status" => $ci_host.'/repo/{git_type}/{username}/{repo_name}/status',
                "activate@post" => $ci_host.'/repo/{git_type}/{username}/{repo_name}/activate',
                'deactivate@post' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/deactivate',
                'start@post' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/star',
                'unstar@post' => $ci_host.'/repo/{git_type}/{username}/{repo_name}/unstar',
            ],
            'builds' => [
                "list" => $ci_host.'/builds',
                "find" => $ci_host.'/build/{build_id}',
                "cancel@post" => $ci_host.'/build/{build_id}/cancel',
                "restart@post" => $ci_host.'/build/{build_id}/restart',
                'log' => [
                    'find' => $ci_host.'/build/{build_id}/log',
                    'delete' => $ci_host.'/build/{build_id}/log'
                ],
            ],
            "owner" => [
                'find' => $ci_host.'/owner/{git_type}/{username}',
                'active' => $ci_host.'/owner/{git_type}/{username}/active',
                'activeByGitHubId' => $ci_host.'/owner/github_id/{id}/active',
                'activeByCodingId' => $ci_host.'/owner/coding_id/{id}/active',
                'activeByGiteeId' => $ci_host.'/owner/gitee_id/{id}/active',
                'activeByAliyunCodeId' => $ci_host.'/owner/aliyuncode_id/{id}/active',
            ],
            'orgs' => [
                'list' => $ci_host.'/orgs/{git_type}', // Returns a list of organizations the current user is a member of.
                'find' => $ci_host.'/org/{git_tyep}/{org_name}'
            ],
        ];

        ksort($array);

        $array['sitemap'] = Env::get('CI_HOST').'/sitemap';

        return $array;
    }
}
