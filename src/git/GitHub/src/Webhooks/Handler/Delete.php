<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Abstracts\DeleteAbstract;

class Delete extends DeleteAbstract
{
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Delete::handle($webhooks_content);

        $this->pustomize($context, 'github');
    }
}
