<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\Release;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

class ReleaseContext extends Context
{
    /**
     * if the action was `edited`.
     */
    public $changes;

    public Release $release;

    use ContextTrait;
}
