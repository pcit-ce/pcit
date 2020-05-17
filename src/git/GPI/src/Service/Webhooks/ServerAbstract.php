<?php

declare(strict_types=1);

namespace PCIT\GPI\Service\Webhooks;

abstract class ServerAbstract implements ServerInterface
{
    public $cache_key = '/pcit/webhooks';

    /**
     * 仅接收收据,除有效性验证外不做任何处理.
     *
     * @param $content
     *
     * @return bool|int
     *
     * @throws \Exception
     */
    public function pushCache(string $type, $content)
    {
        return \Cache::store()->lpush($this->cache_key, json_encode([$this->git_type, $type, $content]));
    }

    /**
     * 获取一条缓存数据.
     *
     * @return string|false
     *
     * @throws \Exception
     */
    public function getCache()
    {
        return \Cache::store()->rPop($this->cache_key);
    }

    /**
     * 回滚.
     *
     * @return bool|int
     *
     * @throws \Exception
     */
    public function rollback(string $content)
    {
        return \Cache::store()->lPush($this->cache_key, $content);
    }

    /**
     * 处理成功，存入成功队列.
     *
     * @return bool|int
     *
     * @throws \Exception
     */
    public function pushSuccessCache(string $content)
    {
        return \Cache::store()->lPush($this->cache_key.'_success', $content);
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
     * @return bool|int
     *
     * @throws \Exception
     */
    public function pushErrorCache(string $content)
    {
        return \Cache::store()->lPush($this->cache_key.'_error', $content);
    }

    /**
     * 获取失败的队列.
     */
    public function getErrorCache()
    {
        return [];
    }
}
