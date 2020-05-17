<?php

declare(strict_types=1);

namespace PCIT\Coding\Service\Webhooks;

use Exception;
use PCIT\GPI\Service\Webhooks\ServerAbstract;

class Server extends ServerAbstract
{
    protected $git_type = 'coding';

    public function server()
    {
        $content = \Request::GetContent();

        $type = \Request::getHeader('X-Coding-Event') ?? 'undefined';

        try {
            return $this->pushCache($type, $content);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function secret(string $content): void
    {
    }
}
