<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components\CheckPullRequest;

class Repo
{
    public int $id;
    public string $url;
    public string $name;
}
