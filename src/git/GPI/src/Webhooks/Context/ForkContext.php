<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\Repository;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * A user forks a repository.
 */
class ForkContext extends Context
{
    public Repository $forkee;

    use ContextTrait;
}
