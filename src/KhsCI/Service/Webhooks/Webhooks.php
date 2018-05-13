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
    private static $git_type = 'github';

    /**
     * @return void
     * @throws Exception
     */
    public function github()
    {
        $signature = Request::getHeader('X-Hub-Signature');
        $type = Request::getHeader('X-Github-Event') ?? 'undefined';
        $content = file_get_contents('php://input');
        $secret = Env::get('CI_WEBHOOKS_TOKEN') ?? md5('khsci');

        list($algo, $github_hash) = explode('=', $signature, 2);

        $serverHash = hash_hmac($algo, $content, $secret);

        // return $this->$type($content);

        if ($github_hash === $serverHash) {
            try {
                self::pushCache($type, $content);
            } catch (Error | Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }

        throw new Exception('', 402);
    }

    /**
     * @throws Exception
     */
    public function github_app()
    {
        self::$git_type = 'github_app';

        $this->github();
    }

    public function coding()
    {

    }

    public function gitee()
    {

    }

    /**
     * 仅接收收据,除有效性验证外不做任何处理
     *
     * @param string $type
     * @param        $content
     *
     * @throws Exception
     */
    private static function pushCache(string $type, $content)
    {
        Cache::connect()->lpush('webhooks', json_encode([static::$git_type, $type, $content]));
    }
}
