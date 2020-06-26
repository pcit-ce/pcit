<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Owner;

/**
 * @property int    $installation_id
 * @property int    $rid
 * @property string $repo_full_name
 * @property Owner  $owner
 * @property string $ref_type
 * @property string $ref
 */
class DeleteContext extends Context
{
}
