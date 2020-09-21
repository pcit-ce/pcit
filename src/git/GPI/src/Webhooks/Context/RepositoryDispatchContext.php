<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * @property string $branch
 * @property object $client_payload
 */
class RepositoryDispatchContext extends Context
{
    public $client_payload;

    // use ContextTrait;
}
