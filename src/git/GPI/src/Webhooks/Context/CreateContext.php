<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

/**
 * A Git branch or tag is created.
 *
 * @property string $master_branch
 * @property string $description
 */
class CreateContext extends DeleteContext
{
}
