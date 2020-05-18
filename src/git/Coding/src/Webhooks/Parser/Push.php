<?php

declare(strict_types=1);

namespace PCIT\Coding\Webhooks\Parser;

use PCIT\GitHub\Webhooks\Parser\Push as GitHubPushParser;
use PCIT\GPI\Webhooks\Context;

class Push extends GitHubPushParser
{
    use Common;

    public static function handle(string $webhooks_content): Context
    {
        $context = parent::handle($webhooks_content);

        $context->repo_full_name = self::handle_repo_full_name($context->repo_full_name);

        return $context;
    }
}
