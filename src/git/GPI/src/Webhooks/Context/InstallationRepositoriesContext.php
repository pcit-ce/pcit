<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Sender;

/**
 * @property int     $installation_id
 * @property string  $action
 * @property array   $repo
 * @property Sender  $sender
 * @property Account $account
 */
class InstallationRepositoriesContext extends Context
{
}
