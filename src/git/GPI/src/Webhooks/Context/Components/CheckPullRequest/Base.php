<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components\CheckPullRequest;

/**
 * @property string $ref
 * @property string $sha
 */
class Base
{
    public Repo $repo;
}
