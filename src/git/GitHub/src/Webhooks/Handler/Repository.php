<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Abstracts\RepositoryAbstract;

class Repository extends RepositoryAbstract
{
    public $git_type = 'github';

    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Repository::parse($webhooks_content);

        $this->pustomize($context, $this->git_type);
    }
}
