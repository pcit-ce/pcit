<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components\PullRequest;

use PCIT\GPI\Webhooks\Context\Components\Kernel;

/**
 * @property string     $label
 * @property string     $ref
 * @property string     $sha
 * @property Owner      $user
 * @property Repository $repo
 */
class Base extends Kernel
{
}
