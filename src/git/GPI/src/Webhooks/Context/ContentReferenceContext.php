<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\ContentReference;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

class ContentReferenceContext extends Context
{
    public ContentReference $content_reference;

    use ContextTrait;
}
