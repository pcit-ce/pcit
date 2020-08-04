<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

use PCIT\GPI\Webhooks\Context\Components\User\Owner;

/**
 * @property string $default_branch
 * @property int    $id
 * @property string $name
 * @property string $full_name
 * @property bool   $private
 * @property Owner  $owner
 */
class Repository extends Kernel
{
}
