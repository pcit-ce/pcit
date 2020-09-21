<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * Activity related to repositories being added to a GitHub App installation.
 *
 * @property "added"|"removed" $action
 */
class InstallationRepositoriesContext extends Context
{
    public string $repository_selection;
    /** @var \PCIT\GPI\Webhooks\Context\Components\InstallationRepositories[] */
    public $repositories_added;
    /** @var \PCIT\GPI\Webhooks\Context\Components\InstallationRepositories[] */
    public $repositories_removed;

    use ContextTrait;
}
