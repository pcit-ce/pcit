<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Webhooks;

use Exception;
use PCIT\Framework\Support\Cache;
use PCIT\Framework\Support\Env;

/**
 * @see https://developer.github.com/webhooks/#events
 */
class Client
{
    /**
     * @var string
     */
    private $git_type = 'github';

    /**
     * @var string
     */
    public $cache_key = 'webhooks';

    public function __construct()
    {
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public function server()
    {
        $type = \Request::getHeader('X-Github-Event') ?? 'undefined';
        // $content = file_get_contents('php://input');

        $content = \Request::getContent();

        $this->secret($content);

        try {
            return $this->pushCache($type, $content);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $content
     *
     * @throws Exception
     */
    private function secret(string $content): void
    {
        if (env('CI_WEBHOOKS_DEBUG', false)) {
            return;
        }

        $secret = env('CI_WEBHOOKS_TOKEN', null) ?? md5('khsci');

        $signature = \Request::getHeader('X-Hub-Signature');

        list($algo, $github_hash) = explode('=', $signature, 2);

        $serverHash = hash_hmac($algo, $content, $secret);

        if ($github_hash === $serverHash) {
            return;
        }

        throw new Exception('', 402);
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
    private function pushCache(string $type, $content)
    {
        return Cache::store()->lpush($this->cache_key, json_encode([$this->git_type, $type, $content]));
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
        return Cache::store()->rPop($this->cache_key);
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
        return Cache::store()->lPush($this->cache_key, $content);
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
        return Cache::store()->lPush($this->cache_key.'_success', $content);
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
        return Cache::store()->lPush($this->cache_key.'_error', $content);
    }

    /**
     * 获取失败的队列.
     */
    public function getErrorCache()
    {
        return [];
    }
}
