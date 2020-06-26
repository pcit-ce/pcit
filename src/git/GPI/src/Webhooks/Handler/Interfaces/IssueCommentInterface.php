<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Interfaces;

interface IssueCommentInterface
{
    public function handle(string $webhooks_content): void;
}
