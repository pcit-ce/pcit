<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

use PCIT\GPI\Webhooks\Context\Components\User\User;

class Issue
{
    public User $user;

    public int $number;

    public int $id;

    public string $title;

    /** @var \PCIT\GPI\Webhooks\Context\Components\Label[] */
    public array $labels;

    /** @var \PCIT\GPI\Webhooks\Context\Components\User\Assignee[] */
    public array $assignees;

    public string $body;
}
