<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;

/**
 * @property "created"|"deleted"|"archived"|"unarchived"|"edited"|"renamed"|"transferred"|"publicized"|"privatized" $action
 */
class RepositoryContext extends Context
{
}
