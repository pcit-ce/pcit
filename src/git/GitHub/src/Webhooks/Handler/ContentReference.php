<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Abstracts\ContentReferenceAbstract;

class ContentReference extends ContentReferenceAbstract
{
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\ContentReference::handle($webhooks_content);

        $this->pustomize($context, 'github');
    }
}
