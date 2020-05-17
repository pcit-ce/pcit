<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Webhooks;

use Exception;
use PCIT\GPI\Service\Webhooks\ServerAbstract;

/**
 * @see https://developer.github.com/webhooks/#events
 */
class Server extends ServerAbstract
{
    /**
     * @var string
     */
    protected $git_type = 'github';

    /**
     * @return int
     *
     * @throws \Exception
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
     * @throws \Exception
     */
    public function secret(string $content): void
    {
        if (env('CI_WEBHOOKS_DEBUG', false)) {
            return;
        }

        $secret = env('CI_WEBHOOKS_TOKEN', null) ?? md5('pcit-secret');

        $signature = \Request::getHeader('X-Hub-Signature');

        list($algo, $github_hash) = explode('=', $signature, 2);

        $serverHash = hash_hmac($algo, $content, $secret);

        if ($github_hash === $serverHash) {
            return;
        }

        throw new Exception('', 402);
    }
}
