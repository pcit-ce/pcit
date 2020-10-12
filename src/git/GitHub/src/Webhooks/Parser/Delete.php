<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\DeleteContext;

class Delete
{
    public static function handle(string $webhooks_content): DeleteContext
    {
        \Log::info('Receive event', ['type' => 'delete']);

        return new DeleteContext([], $webhooks_content);
    }
}
