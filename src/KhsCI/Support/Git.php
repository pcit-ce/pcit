<?php

declare(strict_types=1);

namespace KhsCI\Support;

use Exception;

class Git
{
    const SUPPORT_ALIYUN = 'aliyun';

    const SUPPORT_CODING = 'coding';

    const SUPPORT_GITEE = 'gitee';

    const SUPPORT_GITHUB = 'github';

    const SUPPORT_GITHUB_APP = 'github_app';

    /**
     * @param string $type
     * @param string $repo_full_name
     * @param bool   $ssh
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getUrl(string $type, string $repo_full_name, bool $ssh = false)
    {
        list($username, $repo) = explode('/', $repo_full_name);

        switch ($type) {
            case 'aliyun':
                $url = 'https://code.aliyun.com/'.$repo_full_name;

                if ($ssh) {
                    $url = 'git@code.aliyun.com:'.$repo_full_name;
                }

                break;
            case 'coding':
                $url = 'https://coding.net/u/'.$username.'/p/'.$repo.'/git';

                if ($ssh) {
                    $url = 'git@git.coding.net:'.$repo_full_name;
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
            case 'github_app':
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
     * @param string $type
     * @param string $repo_full_name
     * @param int    $pull_id
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getPullRequestUrl(string $type, string $repo_full_name, int $pull_id)
    {
        $base_url = self::getUrl($type, $repo_full_name);

        switch ($type) {
            case 'coding':
                $url = null;

                break;
            case 'gitee':
                $url = $base_url.'/pulls/'.$pull_id;

                break;
            case 'github':
                $url = null;

                break;
            case 'github_app':
                $url = null;

                break;
            default:
                throw new Exception('Not Support', 500);
        }

        $common_url = $base_url.'/pull/'.$pull_id;

        return $url ?? $common_url;
    }

    /**
     * @param string $type
     * @param string $repo_full_name
     * @param string $issue_id
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getIssueUrl(string $type, string $repo_full_name, string $issue_id)
    {
        $base_url = self::getUrl($type, $repo_full_name);

        switch ($type) {
            case 'aliyun':
                $url = null;

                break;
            case 'coding':
                $url = $base_url.'/topic/'.$issue_id;

                break;
            case 'gitee':

                $url = null;

                break;
            case 'github':
                $url = null;

                break;
            case 'github_app':
                $url = null;

                break;
            default:
                throw new Exception('Not Support', 500);
        }

        $common_url = $base_url.'/issues/'.$issue_id;

        return $url ?? $common_url;
    }

    /**
     * @param string $type
     * @param string $repo_full_name
     * @param string $commit_id
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getCommitUrl(string $type, string $repo_full_name, string $commit_id)
    {
        $base_url = self::getUrl($type, $repo_full_name);

        switch ($type) {
            case 'aliyun':
                $url = null;

                break;
            case 'coding':
                $url = null;

                break;
            case 'gitee':
                $url = null;

                break;
            case 'github':
                $url = null;

                break;

            case 'github_app':
                $url = null;

                break;

            default:
                throw new Exception('Not Support', 500);
        }

        $common_url = $base_url.'/commit/'.$commit_id;

        return $url ?? $common_url;
    }

    /**
     * @param string $type
     * @param string $repo_full_name
     * @param string $commit_id branch_name commit_id
     * @param string $file_name
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getRawUrl(string $type,
                                     string $repo_full_name,
                                     string $commit_id,
                                     string $file_name)
    {
        switch ($type) {
            case 'aliyun':
                $url = null;

                break;
            case 'coding':
                $url = null;

                break;
            case 'gitee':
                $url = null;

                break;
            case 'github':
                $url = 'https://raw.githubusercontent.com/'.$repo_full_name.'/'.$commit_id.'/'.$file_name;

                break;

            case 'github_app':
                $url = 'https://raw.githubusercontent.com/'.$repo_full_name.'/'.$commit_id.'/'.$file_name;

                break;
            default:
                throw new Exception('Not Support', 500);
        }

        $common_url = self::getUrl($type, $repo_full_name).'/raw/'.$commit_id.'/'.$file_name;

        return $url ?? $common_url;
    }

    /**
     * @param string $git_type
     *
     * @return string
     * @throws Exception
     */
    public static function getApiUrl(string $git_type)
    {
        switch ($git_type) {
            case 'aliyun':
                $url = '';

                break;
            case 'coding':
                $url = 'open.coding.net/api';

                break;

            case 'gitee':
                $url = 'gitee.com/api/v5';

                break;

            case 'github':
                $url = 'api.github.com';

                break;
            case 'github_app':
                $url = 'api.github.com';

                break;
            default:
                throw new Exception('Not Support', 500);
        }

        return 'https://'.$url;
    }
}
