<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

class Label
{
    public int $id;

    public string $name;

    public string $color;

    public bool $default;
}
