<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Owner;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Sender;

/**
 * @property int               $installation_id
 * @property "added"|"removed" $action
 * @property array             $repo
 * @property Sender            $sender
 * @property Owner             $owner
 */
class InstallationRepositoriesContext extends Context
{
}
