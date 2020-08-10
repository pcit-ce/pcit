<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Abstracts\ContentAbstract;

class Content extends ContentAbstract
{
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Content::handle($webhooks_content);

        $this->pustomize($context, 'github');
    }
}
