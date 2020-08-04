<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\User\Owner;

/**
 * @property int            $installation_id
 * @property int            $rid
 * @property string         $repo_full_name
 * @property "branch"|"tag" $ref_type
 * @property Owner          $owner
 */
class CreateContext extends Context
{
}
