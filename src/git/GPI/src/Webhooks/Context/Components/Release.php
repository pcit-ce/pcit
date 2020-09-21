<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

use PCIT\GPI\Webhooks\Context\Components\User\User;

class Release
{
    public int $id;

    public string $tag_name;

    public string $target_commitish;

    public $name;

    public bool $draft;

    public User $author;

    public bool $prerelease;

    public $body;
}
