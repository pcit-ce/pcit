<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

class PageBuildContext extends Context
{
    public int $id;

    public $build;

    use ContextTrait;
}
