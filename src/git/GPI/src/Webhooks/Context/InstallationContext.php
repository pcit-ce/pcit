<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;

/**
 * @property int                                                                  $installation_id
 * @property "created"|"deleted"|"suspend"|"unsuspend"|"new_permissions_accepted" $action
 * @property array                                                                $repositories
 * @property Sender                                                               $sender
 * @property Account                                                              $account
 */
class InstallationContext extends Context
{
}
