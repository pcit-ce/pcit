<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

use PCIT\GPI\Webhooks\Context\Components\PullRequest\Base;
use PCIT\GPI\Webhooks\Context\Components\PullRequest\Head;
use PCIT\GPI\Webhooks\Context\Components\User\User;

class PullRequest
{
    public int $number;

    public string $title;

    public User $user;

    public string $body;

    public Head $head;

    public Base $base;

    public string $created_at;

    public ?string $updated_at;
}
