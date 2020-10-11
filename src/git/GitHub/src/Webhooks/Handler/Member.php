<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Abstracts\MemberAbstract;

class Member extends MemberAbstract
{
    /**
     * `added` `deleted` `edited` `removed`.
     */
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Member::handle($webhooks_content);

        $this->pustomize($context, 'github');
    }
}
