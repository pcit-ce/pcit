<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\ContentReferenceContext;

class ContentReference
{
    /**
     * @see https://developer.github.com/apps/using-content-attachments/
     */
    public static function handle(string $webhooks_content): ContentReferenceContext
    {
        return new ContentReferenceContext([], $webhooks_content);
    }
}
