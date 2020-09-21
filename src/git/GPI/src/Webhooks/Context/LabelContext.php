<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\Label;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * Activity related to an issue.
 *
 * @property "created"|"edited"|"deleted" $action
 */
class LabelContext extends Context
{
    public Label $label;

    /**
     * The changes to the label if the action was `edited`.
     */
    public $changes;

    use ContextTrait;
}
