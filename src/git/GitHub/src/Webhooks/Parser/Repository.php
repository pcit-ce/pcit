<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\RepositoryContext;

class Repository
{
    public static function parse(string $webhooks_content): RepositoryContext
    {
        return $context = new RepositoryContext([], $webhooks_content);
    }
}
