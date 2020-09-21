<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * @property string $branch
 */
class RepositoryDispatchContext extends Context
{
    /** @var object */
    public $client_payload;

    // use ContextTrait;
}
