<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Parser\Interfaces;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\TagContext;

interface PushInterface
{
    /**
     * @return PushContext|TagContext
     */
    public static function handle(string $webhooks_content): Context;

    public static function tag(string $tag, string $webhooks_content): TagContext;
}
