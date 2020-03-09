<?php

declare(strict_types=1);

namespace PCIT\Support;

use Exception;

class Git
{
    const SUPPORT_ALIYUN = 'aliyun';

    const SUPPORT_CODING = 'coding';

    const SUPPORT_GITEE = 'gitee';

    const SUPPORT_GITHUB = 'github';

    const SUPPORT_GOGS = 'gogs';

    const SUPPORT_GIT_ARRAY = [
        'aliyun',
        'coding',
        'gitee',
        'github',
        'gogs',
    ];

    /**
     * @param $git_type
     *
     * @throws \Exception
     */
    public static function checkGit($git_type): void
    {
        if (!\in_array($git_type, self::SUPPORT_GIT_ARRAY, true)) {
            throw new Exception('Not Found', 404);
        }
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public static function getUrl(string $type, string $repo_full_name, bool $ssh = false)
    {
        list($username, $repo) = explode('/', $repo_full_name);

        self::checkGit($type);

        switch ($type) {
            case 'aliyun':
                $url = 'https://code.aliyun.com/'.$repo_full_name;

                if ($ssh) {
                    $url = 'git@code.aliyun.com:'.$repo_full_name;
                }

                break;
            case 'coding':
                $url = 'https://e.coding.net/'.$username.'/'.$repo;

                if ($ssh) {
                    $url = 'git@e.coding.net:'.$repo_full_name;
                }

                break;
            case 'gitee':
                $url = 'https://gitee.com/'.$repo_full_name;

                if ($ssh) {
                    $url = 'git@gitee.com:'.$repo_full_name;
                }

                break;
            case 'github':
                $url = 'https://github.com/'.$repo_full_name;

                if ($ssh) {
                    $url = 'git@githun.com:'.$repo_full_name;
                }

                break;
            default:
                throw new Exception('Not Support', 500);
        }

        return $url;
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public static function getPullRequestUrl(string $type, string $repo_full_name, int $pull_id)
    {
        $base_url = self::getUrl($type, $repo_full_name);

        self::checkGit($type);

        switch ($type) {
            case 'gitee':
                $url = $base_url.'/pulls/'.$pull_id;

                break;

            default:
                $url = $base_url.'/pull/'.$pull_id;
        }

        return $url;
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public static function getIssueUrl(string $type, string $repo_full_name, string $issue_id)
    {
        $base_url = self::getUrl($type, $repo_full_name);

        self::checkGit($type);

        switch ($type) {
            case 'coding':
                $url = $base_url.'/topic/'.$issue_id;

                break;

            default:
                $url = $base_url.'/issues/'.$issue_id;
        }

        return $url;
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public static function getCommitUrl(string $type, string $repo_full_name, string $commit_id)
    {
        $base_url = self::getUrl($type, $repo_full_name);

        self::checkGit($type);

        switch ($type) {
            default:
                $url = $base_url.'/commit/'.$commit_id;
        }

        return $url;
    }

    /**
     * @param string $repo_full_name username/reponame
     * @param string $commit_id      branch_name commit_id
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function getRawUrl(string $type,
                                     string $repo_full_name,
                                     string $commit_id,
                                     string $file_name)
    {
        self::checkGit($type);

        switch ($type) {
            case 'github':
                $url = 'https://raw.githubusercontent.com/'.$repo_full_name.'/'.$commit_id.'/'.$file_name;

                break;
            default:
                $url = self::getUrl($type, $repo_full_name).'/raw/'.$commit_id.'/'.$file_name;
        }

        return $url;
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public static function getApiUrl(string $git_type)
    {
        self::checkGit($git_type);

        switch ($git_type) {
            case 'aliyun':
                $url = '';

                break;
            case 'coding':
                // 支持 Coding 企业版
                $url = env('CI_CODING_HOST', null) ?? env('CI_CODING_TEAM').'.coding.net';
                $url .= '/api';
                break;

            case 'gitee':
                $url = 'gitee.com/api/v5';

                break;

            case 'github':
                $url = 'api.github.com';

                break;

            default:
                throw new Exception('Not Support', 500);
        }

        return 'https://'.$url;
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public static function getClassName(string $git_type)
    {
        self::checkGit($git_type);

        switch ($git_type) {
            case 'github':
                $class_name = 'GitHub';

                break;

            default:
                $class_name = ucfirst($git_type);
        }

        return $class_name;
    }
}
