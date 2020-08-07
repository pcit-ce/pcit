<?php

declare(strict_types=1);

namespace PCIT\Coding\Webhooks\Handler;

use PCIT\Coding\Webhooks\Parser\Push as PushParser;
use PCIT\DisableHandler;
use PCIT\GPI\Webhooks\Context\TagContext;
use PCIT\GPI\Webhooks\Handler\Abstracts\PushAbstract;

class Push extends PushAbstract
{
    public $git_type = 'coding';

    public function handle(string $webhooks_content): void
    {
        $context = PushParser::handle($webhooks_content);

        DisableHandler::handle($context->repo_full_name, $this->git_type);

        if ($context->tag ?? null) {
            $this->tag($context);

            return;
        }

        $this->handlePush($context, $this->git_type);
    }

    public function tag(TagContext $context): void
    {
        $this->handleTag($context, $this->git_type);
    }
}
