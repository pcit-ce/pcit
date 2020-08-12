<?php

declare(strict_types=1);

namespace PCIT\GPI\Service\Webhooks;

abstract class ServerAbstract implements ServerInterface
{
    protected $git_type;

    public $cache_key = '/pcit/webhooks';

    public function getWebhookContent()
    {
        return \Request::GetContent();
    }

    public function getEventType(string $event_header): string
    {
        $event_type = explode(' ', \Request::getHeader($event_header))[0] ?? 'undefined';

        return strtolower($event_type);
    }

    /**
     * 验证 webhooks 内容之后存入缓存.
     */
    public function storeAfterVerify(string $event_type)
    {
        return $this->pushCache($event_type, $this->getWebhookContent());
    }

    public function verify(string $signature_header): void
    {
        if (config('git.webhooks.debug')) {
            return;
        }

        $secret = config('git.webhooks.token');

        $signature = \Request::getHeader($signature_header);

        list($algo, $client_hash) = explode('=', $signature, 2);

        $server_hash = hash_hmac($algo, $this->getWebhookContent(), $secret);

        if ($client_hash === $server_hash) {
            return;
        }

        throw new \Exception('', 402);
    }

    /**
     * 仅接收收据,除有效性验证外不做任何处理.
     *
     * @param $content
     *
     * @throws \Exception
     *
     * @return bool|int
     */
    public function pushCache(string $type, $content)
    {
        return \Cache::lpush($this->cache_key, json_encode([$this->git_type, $type, $content]));
    }

    /**
     * 获取一条缓存数据.
     *
     * @throws \Exception
     *
     * @return false|string
     */
    public function getCache()
    {
        return \Cache::rPop($this->cache_key);
    }

    /**
     * 回滚.
     *
     * @throws \Exception
     *
     * @return bool|int
     */
    public function rollback(string $content)
    {
        return \Cache::lPush($this->cache_key, $content);
    }

    /**
     * 处理成功，存入成功队列.
     *
     * @throws \Exception
     *
     * @return bool|int
     */
    public function pushSuccessCache(string $content)
    {
        return \Cache::lPush($this->cache_key.'_success', $content);
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
     * @throws \Exception
     *
     * @return bool|int
     */
    public function pushErrorCache(string $content)
    {
        return \Cache::lPush($this->cache_key.'_error', $content);
    }

    /**
     * 获取失败的队列.
     */
    public function getErrorCache()
    {
        return [];
    }
}
