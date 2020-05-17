<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\ContentContext;

class Content
{
    /**
     * @see https://developer.github.com/apps/using-content-attachments/
     */
    public static function handle(string $webhooks_content): ContentContext
    {
        $obj = json_decode($webhooks_content);

        $action = $obj->action;
        $content_reference = $obj->content_reference;
        $content_reference_id = $content_reference->id;
        $content_reference_reference = $content_reference->reference;
        $installation_id = $obj->installation->id;

        $context = new ContentContext([], $webhooks_content);

        $context->action = $action;
        $context->content_reference_id = $content_reference_id;
        $context->content_reference_reference = $content_reference_reference;
        $context->installation_id = $installation_id;

        return $context;
    }
}
