<?php

declare(strict_types=1);

namespace KhsCI\Service\Gist;

use Exception;
use KhsCI\Service\CICommon;

class GitHubClient
{
    use CICommon;

    /**
     * List a user's gists
     *
     * @param string|null $username
     * @param string      $since YYYY-MM-DDTHH:MM:SSZ
     *
     * @return mixed
     * @throws Exception
     */
    public function list(string $username = null, string $since)
    {
        $url = self::$api_url.'/gists';

        if ($username) {
            $url = self::$api_url.'/users/'.$username.'/gists';
        }

        return self::$curl->get($url);
    }

    /**
     * GitHub 所有用户发布的最新 30*100 条 gists
     */
    public function all()
    {
        return [];
    }

    public function ListStarred()
    {

    }

    public function find()
    {

    }

    public function getSpecificRevision()
    {

    }

    public function create()
    {

    }

    public function edit()
    {

    }

    public function listCommits()
    {

    }

    public function star()
    {

    }

    public function unstar()
    {

    }

    public function isStarred()
    {

    }

    public function fork()
    {

    }

    public function listFork()
    {

    }

    public function delete()
    {

    }
}
