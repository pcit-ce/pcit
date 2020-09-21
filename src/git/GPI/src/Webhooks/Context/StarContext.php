<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;

class StarContext extends Context
{
    public ?string $starred_at;
}
