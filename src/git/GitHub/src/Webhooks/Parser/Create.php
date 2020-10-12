<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\CreateContext;

class Create
{
    public static function handle(string $webhooks_content): CreateContext
    {
        return new CreateContext([], $webhooks_content);
    }
}
