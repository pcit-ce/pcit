<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components\PullRequest;

use PCIT\GPI\Webhooks\Context\Components\Kernel;
use PCIT\GPI\Webhooks\Context\Components\Repository;
use PCIT\GPI\Webhooks\Context\Components\User\Owner;

/**
 * @property string     $label
 * @property string     $ref
 * @property string     $sha
 * @property Owner      $user
 * @property Repository $repo
 */
class Head extends Kernel
{
}
