<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

abstract class Kernel
{
    public $obj;

    public function __construct($obj)
    {
        $this->obj = $obj;
    }

    public function __get($name)
    {
        return $this->obj->$name ?? null;
    }
}
