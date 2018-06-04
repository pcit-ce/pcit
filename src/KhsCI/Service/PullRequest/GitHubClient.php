<?php

declare(strict_types=1);

namespace KhsCI\Service\PullRequest;

use Exception;
use KhsCI\Service\CICommon;

class GitHubClient
{
    use CICommon;

    private $is_update;

    private $header = [
        'Accept' => 'application/vnd.github.symmetra-preview+json',
    ];

    /**
     * @param string $username
     * @param string $repo_name
     * @param string $state     Either open, closed, or all to filter by state. Default: open
     * @param string $head
     * @param string $base
     * @param string $sort      What to sort results by. Can be either created, updated, popularity (comment count) or
     *                          long-running (age, filtering by pulls updated in the last month). Default: created
     * @param string $direction The direction of the sort. Can be either asc or desc. Default: desc when sort is
     *                          created or sort is not specified, otherwise asc.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function list(string $username,
                         string $repo_name,
                         string $state = null,
                         string $head = null,
                         string $base = null,
                         string $sort = null,
                         string $direction = null)
    {
        $url = $this->api_url.implode('/', ['/repos', $username, $repo_name, 'pulls']);

        $data = [
            'state' => $state,
            'head' => $head,
            'base' => $base,
            'sort' => $sort,
            'direction' => $direction,
        ];

        return $this->curl->get($url.'?'.http_build_query($data));
    }

    /**
     * Get a single pull request.
     *
     * @param string $username
     * @param string $repo_name
     * @param int    $pr_num
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function get(string $username, string $repo_name, int $pr_num)
    {
        $url = $this->api_url.implode('/', ['/repos', $username, $repo_name, 'pulls', $pr_num]);

        return $this->curl->get($url);
    }

    /**
     * @param string $username
     * @param string $repo_name
     * @param int    $from_issue
     * @param string $title
     * @param string $head
     * @param string $base
     * @param string $body
     * @param bool   $maintainer_can_modify
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function create(string $username,
                           string $repo_name,
                           int $from_issue = 0,
                           string $title,
                           string $head,
                           string $base,
                           string $body = null,
                           bool $maintainer_can_modify = true)
    {
        $url = $this->api_url.implode('/', ['/repos', $username, $repo_name, '/pulls']);

        $data = [
            'title' => $title,
            'body' => $body,
            'head' => $head,
            'base' => $base,
            'maintainer_can_modify' => $maintainer_can_modify,
        ];

        if (0 !== $from_issue) {
            array_shift($data);
            array_shift($data);

            $array['issue'] = $from_issue;
        }

        if ($this->is_update) {
            return $this->curl->patch($url, json_encode($data));
        }

        return $this->curl->post($url, json_encode($data));
    }

    /**
     * @param string      $username
     * @param string      $repo_name
     * @param int         $from_issue
     * @param string      $title
     * @param string      $head
     * @param string      $base
     * @param string|null $body
     * @param bool        $maintainer_can_modify
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function update(string $username,
                           string $repo_name,
                           int $from_issue = 0,
                           string $title,
                           string $head,
                           string $base,
                           string $body = null,
                           bool $maintainer_can_modify = true)
    {
        $this->is_update = true;
        $output = $this->create(...func_get_args());
        $this->is_update = false;

        return $output;
    }

    /**
     * @param string $username
     * @param string $repo_name
     * @param int    $pr_num
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listCommits(string $username, string $repo_name, int $pr_num)
    {
        $url = $this->api_url.implode('/', ['/repos', $username, $repo_name, 'pulls', $pr_num, 'commits']);

        return $this->curl->get($url);
    }

    /**
     * List pull requests files.
     *
     * @param string $username
     * @param string $repo_name
     * @param int    $pr_num
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listFiles(string $username, string $repo_name, int $pr_num)
    {
        $url = $this->api_url.implode('/', ['/repos', $username, $repo_name, '/pulls', $pr_num, '/files']);

        return $this->curl->get($url);
    }

    /**
     * Get if a pull request has been merged.
     *
     * @param string $username
     * @param string $repo_name
     * @param        $pr_num
     *
     * @return bool
     *
     * @throws Exception
     */
    public function isMerged(string $username, string $repo_name, $pr_num)
    {
        $url = $this->api_url.implode('/', ['/repos', $username, $repo_name, 'pulls', $pr_num, 'merge']);

        $this->curl->get($url);

        $http_return_code = $this->curl->getCode();

        if (204 === $http_return_code) {
            return true;
        } elseif (404 === $http_return_code) {
            return false;
        }

        return false;
    }

    /**
     * @param string $username
     * @param string $repo_name
     * @param int    $pr_num
     * @param string $commit_title
     * @param string $commit_message
     * @param string $sha
     * @param bool   $rebase
     * @param bool   $squash
     *
     * @return bool|mixed
     *
     * @throws Exception
     */
    public function merge(string $username,
                          string $repo_name,
                          int $pr_num,
                          string $commit_title,
                          string $commit_message,
                          string $sha,
                          bool $rebase = false,
                          bool $squash = false)
    {
        if ($rebase && $squash) {
            throw new Exception('', 500);
        }

        $merge_method = false;

        $rebase && $merge_method = 'rebase';

        $squash && $merge_method = 'squash';

        $url = $this->api_url.implode('/', ['/repos', $username, $repo_name, '/pulls', $pr_num, 'merge']);

        $data = [
            'commit_title' => $commit_title,
            'commit_message' => $commit_message,
            'sha' => $sha,
            'merge_method' => $merge_method,
        ];

        $output = $this->curl->put($url, json_encode($data));

        $http_return_code = $this->curl->getCode();

        if (200 === $http_return_code) {
            return true;
        }

        if (405 === $http_return_code) {
            return $output;
        }

        if (409 === $http_return_code) {
            return $output;
        }

        return $output;
    }
}
