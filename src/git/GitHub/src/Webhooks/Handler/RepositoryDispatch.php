<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Abstracts\RepositoryDispatchAbstract;

class RepositoryDispatch extends RepositoryDispatchAbstract
{
    public $git_type = 'github';

    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\RepositoryDispatch::parse($webhooks_content);

        $this->pustomize($context, $this->git_type);
    }
}
