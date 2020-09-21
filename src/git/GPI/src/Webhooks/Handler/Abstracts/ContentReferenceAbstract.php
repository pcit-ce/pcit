<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\ContentReferenceContext;
use PCIT\GPI\Webhooks\Handler\Handler;

abstract class ContentReferenceAbstract extends Handler
{
    public function pustomize(ContentReferenceContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        $this->callPustomize('ContentReference', $context);
    }
}
