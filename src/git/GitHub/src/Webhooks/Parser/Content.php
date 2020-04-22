<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

class Content
{
    /**
     * @see https://developer.github.com/apps/using-content-attachments/
     */
    public static function handle(string $webhooks_content): array
    {
        $obj = json_decode($webhooks_content);

        $action = $obj->action;
        $content_reference = $obj->content_reference;
        $content_reference_id = $content_reference->id;
        $content_reference_reference = $content_reference->reference;
        $installation_id = $obj->installation->id;

        return compact(
        'action',
        'content_reference_id',
        'content_reference_reference',
        'installation_id');
    }
}
