<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\User\Sender;

/**
 * When someone revokes their authorization of a GitHub App, this event occurs.
 * A GitHub App receives this webhook by default and cannot unsubscribe from this event.
 *
 * @property string $name
 * @property Sender $sender
 */
class GitHubAppAuthorizationContext extends Context
{
}
