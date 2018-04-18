<?php

declare(strict_types=1);

namespace KhsCI\Service\OAuth;

use Curl\Curl;

interface OAuth
{
    public function __construct($config, Curl $curl);

    /**
     * @param null|string $state
     *
     * @return mixed
     */
    public function getLoginUrl(?string $state);

    /**
     * @param string      $code
     * @param null|string $state
     * @param bool        $raw
     *
     * @return mixed
     */
    public function getAccessToken(string $code, ?string $state, bool $raw = false);

    /**
     * 获取用户基本信息.
     *
     * @param string $accessToken
     * @param bool   $raw
     *
     * @return mixed
     */
    public static function getUserInfo(string $accessToken, bool $raw = false);

    /**
     * 获取项目列表.
     *
     * @param string $accessToken
     * @param int    $page
     * @param bool   $raw
     *
     * @return mixed
     */
    public static function getProjects(string $accessToken, int $page = 1, bool $raw = false);

    /**
     * 获取 Webhooks 设置.
     *
     * @param string $accessToken
     * @param string $username
     * @param string $repo
     * @param bool   $raw
     *
     * @return mixed
     */
    public static function getWebhooks(string $accessToken, bool $raw = false, string $username, string $repo);

    /**
     * 配置 Webhooks.
     *
     * @param string $accessToken
     * @param        $data
     * @param string $username
     * @param string $repo
     * @param string $id
     *
     * @return mixed
     */
    public static function setWebhooks(string $accessToken, $data, string $username, string $repo, string $id);

    /**
     * 删除 webhooks.
     *
     * @param string $accessToken
     * @param string $username
     * @param string $repo
     * @param string $id
     *
     * @return mixed
     */
    public static function unsetWebhooks(string $accessToken, string $username, string $repo, string $id);
}
