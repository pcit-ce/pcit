<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Abstracts\CreateAbstract;

class Create extends CreateAbstract
{
    /**
     * Create "repository", "branch", or "tag".
     *
     * @throws \Exception
     */
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Create::handle($webhooks_content);

        $this->pustomize($context, 'github');
    }
}
