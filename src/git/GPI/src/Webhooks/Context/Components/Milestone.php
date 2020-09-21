<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

use PCIT\GPI\Webhooks\Context\Components\User\User;

class Milestone
{
    public int $id;

    public int $number;

    public string $title;

    public string $description;

    public User $creator;
}
