<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Repo;
use Error;
use Exception;
use PCIT\PCIT;
use PCIT\Support\CI;

class Controller
{
    private static $gitType;

    /**
     * 设置 Git 类型.
     *
     * @param mixed ...$arg
     *
     * @return array
     */
    private static function setGitType(...$arg)
    {
        $gitType = $arg[0];
        self::$gitType = $gitType;
        unset($arg[0]);

        return $arg;
    }

    /**
     * 检查 AccessToken.
     *
     * @return bool|null
     *
     * @throws \Exception
     */
    private static function checkAccessToken()
    {
        $header = \Request::getHeader('Authorization') ?? '';

        $access_token = (explode(' ', $header))[1]
            ?? \Session::get(self::$gitType.'.access_token')
            ?? false;

        if (false === $access_token) {
            throw new Exception('access_token not found || Requires authentication || 401 Unauthorized', 401);
        }

        return $access_token;
    }

    /**
     * 获取 Webhooks 列表.
     *
     * @param mixed ...$arg
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public static function list(...$arg)
    {
        $raw = false;

        $arg = self::setGitType(...$arg);

        $access_token = self::checkAccessToken();

        $pcit = app(PCIT::class)->setGitType(static::$gitType)
        ->setAccessToken($access_token);

        $json = $pcit->repo_webhooks->getWebhooks($raw, ...$arg);

        return json_decode($json, true);
    }

    private static function getWebhooksUrl($gitType)
    {
        return config('app.host').'/webhooks/'.$gitType;
    }

    /**
     * 增加 Webhooks，增加之前必须先判断是否已存在，GitHub 除外.
     *
     * @param mixed ...$arg
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public static function add(...$arg)
    {
        $arg = self::setGitType(...$arg);

        $gitType = self::$gitType;

        $method = $gitType.'Json';

        try {
            $data = self::$method(...$arg);
        } catch (Exception | Error $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        $dataObj = json_decode($data);

        if ((!$data) or (!\is_object($dataObj)) or 0 !== json_last_error()) {
            throw new Exception('Invalid request, must include JSON', 422);
        }

        $webhooksUrl = self::getWebhooksUrl($gitType);

        $access_token = self::checkAccessToken();

        $pcit = app(PCIT::class)->setGitType(self::$gitType)
        ->setAccessToken($access_token);

        $getWebhooksStatus = $pcit->repo_webhooks->getStatus($webhooksUrl, ...$arg);

        $repo_full_name = "$arg[1]/$arg[2]";

        if (1 === $getWebhooksStatus) {
            Repo::updateWebhookStatus(1, $gitType, $repo_full_name);

            return ['code' => 200, 'message' => 'Success, But hook already exists on this repository'];
        }

        try {
            $json = $pcit->repo_webhooks->setWebhooks($data, ...$arg);

            // $webhook_id = json_decode($json)->id ?? null;
        } catch (Exception $e) {
            if (422 === $e->getCode()) {
                Repo::updateWebhookStatus(1, $gitType, $repo_full_name);

                return ['code' => 200, 'message' => $e->getMessage()];
            } else {
                return [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ];
            }
        }

        return array_merge([
            'code' => 200,
        ], json_decode($json, true));
    }

    /**
     * 删除 Webhooks.
     *
     * @param mixed ...$arg
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public static function delete(...$arg)
    {
        $arg = self::setGitType(...$arg);

        [, $username, $repo] = $arg;

        $access_token = self::checkAccessToken();

        /** @var \PCIT\PCIT */
        $pcit = app('pcit')->setGitType(self::$gitType)
        ->setAccessToken($access_token);

        $repo_full_name = $username.'/'.$repo;

        // Repo::deactive($username.'/'.$repo, static::$gitType);

        Repo::updateWebhookStatus(0, self::$gitType, $repo_full_name);

        return $pcit->repo_webhooks->unsetWebhooks(...$arg);
    }

    /**
     * 设置 Webhooks 状态缓存.
     *
     * @param mixed ...$arg
     *
     * @throws \Exception
     */
    private static function setBuildStatusCache(int $status = 0, ...$arg): void
    {
        $gitType = self::$gitType;

        $uid = \Session::get($gitType.'.uid');

        $repoFullName = $arg[0].'/'.$arg[1];

        Repo::updateBuildActive($status, $gitType, $repoFullName);
        0 === $status && Repo::updateWebhookStatus($status, $gitType, $repoFullName);

        \Cache::store()->hSet($gitType.'_'.$uid.'_repo_admin', $repoFullName, $status);
    }

    /**
     * 开启构建.
     *
     * @param mixed ...$arg
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function activate(...$arg)
    {
        $arg = self::setGitType(...$arg);

        //首先保证 Webhooks 已设置

        $result = self::add(self::$gitType, ...$arg);

        // 更新缓存 + 更新数据库

        self::setBuildStatusCache(CI::BUILD_ACTIVATE, ...$arg);

        return $result;
    }

    /**
     * 禁用构建，暂时不主动删除 Webhooks.
     *
     * @param array $arg
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function deactivate(...$arg)
    {
        $arg = self::setGitType(...$arg);

        self::setBuildStatusCache(CI::BUILD_DEACTIVATE, ...$arg);

        return [
            'code' => 200,
        ];
    }

    private static function codingJson($project, $repo)
    {
        $url = config('app.host').'/webhooks/coding';

        $rid = (int) Repo::getRid($project, $repo, 'coding');

        $token = config('git.webhooks.token');

        return <<<EOF
{
  "hook_url": "$url",
  "token": "$token",
  "rid": $rid
}
EOF;
    }

    private static function giteeJson()
    {
        $url = config('app.host').'/webhooks/gitee';

        $token = config('git.webhooks.token');

        return <<<EOF
{
  "issues_events": true,
  "merge_requests_events":true,
  "note_events": true,
  "project_id": true,
  "push_events": true,
  "tag_push_events": true,
  "url": "$url",
  "password": "$token"
}
EOF;
    }

    private static function githubJson()
    {
        $url = config('app.host').'/webhooks/github';

        $token = config('git.webhooks.token');

        return <<<EOF
{
  "name": "web",
  "active": true,
  "events": [
    "create",
    "delete",
    "deployment",
    "issues",
    "issue_comment",
    "member",
    "pull_request",
    "pull_request_review",
    "pull_request_review_comment",
    "push",
    "release",
    "repository",
    "repository_vulnerability_alert",
    "team_add"
  ],
  "config": {
    "url": "$url",
    "content_type": "json",
    "secret": "$token",
    "insecure_ssl": "0"
  }
}
EOF;
    }
}
