<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Sender;

/**
 * @property int     $installation_id
 * @property string  $action
 * @property array   $repositories
 * @property Sender  $sender
 * @property Account $account
 */
class InstallationContext extends Context
{
}
