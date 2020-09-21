<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * Activity related to GitHub Packages.
 *
 * @property "published"|"updated" $action
 */
class PackageContext extends Context
{
    public $package;

    use ContextTrait;
}
