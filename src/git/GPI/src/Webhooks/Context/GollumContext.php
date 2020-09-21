<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * A wiki page is created or updated.
 */
class GollumContext extends Context
{
    /** @var \PCIT\GPI\Webhooks\Context\Components\Page[] */
    public $pages;

    use ContextTrait;
}
