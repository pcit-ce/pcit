<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Interfaces;

interface IssuesInterface
{
    public function handle(string $webhooks_content): void;

    public function comment(string $webhooks_content): void;
}
