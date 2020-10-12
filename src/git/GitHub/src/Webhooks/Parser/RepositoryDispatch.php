<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\RepositoryDispatchContext;

class RepositoryDispatch
{
    public static function parse(string $webhooks_content): RepositoryDispatchContext
    {
        return new RepositoryDispatchContext([], $webhooks_content);
    }
}
