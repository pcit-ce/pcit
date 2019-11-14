<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parse;

class Content
{
    /**
     * @see https://developer.github.com/apps/using-content-attachments/
     */
    public static function handle($json_content)
    {
        $obj = json_decode($json_content);

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
