<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

use PCIT\GPI\Webhooks\Context\Components\CheckPullRequest\Base;
use PCIT\GPI\Webhooks\Context\Components\CheckPullRequest\Head;

class PullRequests
{
    public int $number;

    public Head $head;

    public Base $base;
}
