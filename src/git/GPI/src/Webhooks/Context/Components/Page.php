<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

class Page
{
    public string $page_name;

    public string $title;

    public ?string $summary;

    public string $action;

    public string $sha;

    public string $html_url;
}
