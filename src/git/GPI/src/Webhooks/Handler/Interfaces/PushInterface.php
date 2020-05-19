<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Interfaces;

use PCIT\GPI\Webhooks\Context\TagContext;

interface PushInterface
{
    public function handle(string $webhooks_content): void;

    public function tag(TagContext $context): void;
}
