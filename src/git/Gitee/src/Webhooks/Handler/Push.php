<?php

declare(strict_types=1);

namespace PCIT\Gitee\Webhooks\Handler;

use PCIT\Gitee\Webhooks\Parser\Push as PushParser;
use PCIT\GPI\Webhooks\Handler\Abstracts\PushAbstract;
use PCIT\GPI\Webhooks\Handler\DisableHandler;

class Push extends PushAbstract
{
    public $git_type = 'gitee';

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

    public function tag(array $content): void
    {
        $this->handleTag($context, $this->git_type);
    }
}
