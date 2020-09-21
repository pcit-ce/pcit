<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * Activity related to a GitHub App installation.
 *
 * @property "created"|"deleted"|"suspend"|"unsuspend"|"new_permissions_accepted" $action
 */
class InstallationContext extends Context
{
    /** @var \PCIT\GPI\Webhooks\Context\Components\InstallationRepositories[] */
    public $repositories;

    use ContextTrait;
}
