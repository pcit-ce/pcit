<?php

declare(strict_types=1);

namespace KhsCI\Service\Webhooks;

use Error;
use Exception;
use KhsCI\Support\Cache;
use KhsCI\Support\Env;
use KhsCI\Support\Request;

/**
 * Class GitHub.
 *
 * @see https://developer.github.com/webhooks/#events
 */
class Webhooks
{
    /**
     * @var string
     */
    private static $git_type = 'github';

    /**
     * @var string
     */
    public static $cache_key = 'webhooks';

    /**
     * @param string|null $secret
     *
     * @return bool|int
     *
     * @throws Exception
     */
    public function startGitHubServer(string $secret = null)
    {
        $signature = Request::getHeader('X-Hub-Signature');
        $type = Request::getHeader('X-Github-Event') ?? 'undefined';
        $content = file_get_contents('php://input');

        $secret = $secret ?? Env::get('CI_WEBHOOKS_TOKEN', null) ?? md5('khsci');

        list($algo, $github_hash) = explode('=', $signature, 2);

        $serverHash = hash_hmac($algo, $content, $secret);

        // return $this->$type($content);

        if ($github_hash === $serverHash) {
            try {
                return self::pushCache($type, $content);
            } catch (Error | Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }

        throw new Exception('', 402);
    }

    /**
     * @param string $secret
     *
     * @return bool|int
     *
     * @throws Exception
     */
    public function startGitHubAppServer(string $secret = null)
    {
        self::$git_type = 'github_app';

        return $this->startGitHubServer($secret);
    }

    /**
     * @param string $secret
     *
     * @return array
     */
    public function startCodingServer(string $secret = null)
    {
        return [];
    }

    /**
     * @param string $secret
     *
     * @return array
     */
    public function startGiteeServer(string $secret = null)
    {
        return [];
    }

    /**
     * 仅接收收据,除有效性验证外不做任何处理.
     *
     * @param string $type
     * @param        $content
     *
     * @return bool|int
     *
     * @throws Exception
     */
    private static function pushCache(string $type, $content)
    {
        return Cache::connect()->lpush(self::$cache_key, json_encode([static::$git_type, $type, $content]));
    }

    /**
     * 获取一条缓存数据.
     *
     * @return string|false
     *
     * @throws Exception
     */
    public function getCache()
    {
        return Cache::connect()->rPop(self::$cache_key);
    }

    /**
     * 回滚.
     *
     * @param string $content
     *
     * @return bool|int
     *
     * @throws Exception
     */
    public function rollback(string $content)
    {
        return Cache::connect()->lPush(self::$cache_key, $content);
    }

    /**
     * 处理成功，存入成功队列.
     *
     * @param string $content
     *
     * @return bool|int
     *
     * @throws Exception
     */
    public function pushSuccessCache(string $content)
    {
        return Cache::connect()->lPush(self::$cache_key.'_success', $content);
    }

    /**
     * 获取成功的队列.
     */
    public function getSuccessCache()
    {
        return [];
    }

    /**
     * 处理失败，插入失败队列.
     *
     * @param string $content
     *
     * @return bool|int
     *
     * @throws Exception
     */
    public function pushErrorCache(string $content)
    {
        return Cache::connect()->lPush(self::$cache_key.'_error', $content);
    }

    /**
     * 获取失败的队列.
     */
    public function getErrorCache()
    {
        return [];
    }

    public function ping(string $webhooks_json_content): void
    {
    }

    public function push(string $webhooks_json_content): void
    {
    }

    public function tag(string $webhooks_json_content): void
    {
    }

    public function pull_request(string $webhooks_json_content): void
    {
    }

    public function issues(string $webhooks_json_content): void
    {
    }

    public function issue_comment(string $webhooks_json_content): void
    {
    }

    public function watch(string $webhooks_json_content): void
    {
    }

    public function fork(string $webhooks_json_content): void
    {
    }

    public function release(string $webhooks_json_content): void
    {
    }

    public function create(string $webhooks_json_content): void
    {
    }

    public function delete(string $webhooks_json_content): void
    {
    }

    public function member(string $webhooks_json_content): void
    {
    }

    public function installation(string $webhooks_json_content): void
    {
    }

    public function installation_repositories(string $webhooks_json_content): void
    {
    }

    /**
     * @deprecated
     */
    public static function integration_installation(): void
    {
        return;
    }

    /**
     * @deprecated
     */
    public static function integration_installation_repositories(): void
    {
        return;
    }

    public static function check_suite(string $webhooks_json_content): void
    {
        return;
    }

    public static function check_run(string $webhooks_json_content): void
    {
        return;
    }
}
