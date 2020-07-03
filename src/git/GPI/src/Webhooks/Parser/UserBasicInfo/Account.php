<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Parser\UserBasicInfo;

use PCIT\GPI\Webhooks\Context\Components\Kernel;

/**
 * @property string                $login      username
 * @property int                   $id
 * @property string                $avatar_url
 * @property 'User'|'Organization' $type
 * @property bool                  $org
 */
class Account extends Kernel
{
    public function __construct($obj, bool $org = false)
    {
        $this->org = $org;

        parent::__construct($obj);
    }
}
