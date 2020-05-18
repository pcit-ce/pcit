<?php

declare(strict_types=1);

namespace PCIT\Coding\Webhooks\Handler;

use PCIT\Coding\Webhooks\Parser\Push as ParserPush;
use PCIT\GPI\Webhooks\Handler\Abstracts\PushAbstract;

class Push extends PushAbstract
{
    public function handle(string $webhooks_content): void
    {
        $context = ParserPush::handle($webhooks_content);

        $tag = $context->tag ?? null;

        if ($tag) {
            $this->tag($context);

            return;
        }

        $this->handle_push($context, 'coding');
    }

    public function tag(array $content): void
    {
    }
}
