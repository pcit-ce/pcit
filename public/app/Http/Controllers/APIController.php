<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use KhsCI\Support\Env;

class APIController
{
    public function __invoke()
    {
        $ci_host = Env::get('CI_HOST').'/api';

        $array = [
            'user' => [
                'current_user_url' => $ci_host.'/user',
                'find' => $ci_host.'/user/{git_type}/{username}',
                'sync' => $ci_host.'/user/sync',
                'beta_feature' => [
                    'get' => $ci_host.'/user/beta_features',
                    'update@patch' => $ci_host.'/user/beta_feature/{beta_feature.id}',
                    'delete' => $ci_host.'/user/beta_feature/{beta_feature.id}',
                ],
                'active' => $ci_host.'/user/{git_type}/{username}/active', // 返回某用户（或组织）处于活跃状态的仓库列表
            ],
            'repo' => [
                'current_user_repositories_url' => $ci_host.'/repos',
                'user\'s repo' => $ci_host.'/repos/{git_type}/{username}',
                'branch' => [
                    'list@get' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/branches',
                    'find@get' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/branch/{branch.name}',
                ],
                'env_vars' => [
                    'list' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/env_vars',
                    'create@post' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/env_vars',
                    'find' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/env_var/{env_var.id}',
                    'update@patch' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/env_var/{env_var.id}',
                    'delete' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/env_var/{env_var.id}',
                ],
                'settings' => [
                    'list' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/settings',
                    'get' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/setting/{setting.name}',
                    'update@patch' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/setting/{setting.name}',
                ],
                'requests' => [
                    'list' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/requests',
                    'create@post' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/requests',
                    'get' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/request/{requests.id}',
                ],
                'caches' => [
                    'list' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/caches',
                    'delete' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/caches',
                ],
                'crons' => [
                    'list' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/crons',
                    'find' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/cron/{cron.id}',
                    'delete' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/cron/{cron.id}',
                    'findByBranch' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/branch/{branch.name}/cron',
                    'createByBranch@post' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/branch/{branch.name}/cron',
                ],
                'status' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/status',
                'activate@post' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/activate',
                'deactivate@post' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/deactivate',
                'start@post' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/star',
                'unstar@post' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/unstar',
            ],
            'builds' => [
                'current_user_builds_url' => $ci_host.'/builds',
                'listByRepo' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/builds',
                'findByRepoCurrent' => $ci_host.'/repo/{git_type}/{username}/{repo.name}/build/current',
                'find' => $ci_host.'/build/{build_id}',
                'cancel@post' => $ci_host.'/build/{build.id}/cancel',
                'restart@post' => $ci_host.'/build/{build.id}/restart',
                'log' => [
                    'find' => $ci_host.'/build/{build.id}/log',
                    'delete' => $ci_host.'/build/{build.id}/log',
                ],
            ],
            'orgs' => [
                'user_organizations_url' => $ci_host.'/orgs', // Returns a list of organizations the current user is a member of.
                'find' => $ci_host.'/org/{git_type}/{org.name}',
            ],
            'system' => [
                'oauth_client_id' => $ci_host.'/ci/oauth_client_id',
            ],
        ];

        ksort($array);

        $array['sitemap'] = Env::get('CI_HOST').'/sitemap';

        return $array;
    }
}
