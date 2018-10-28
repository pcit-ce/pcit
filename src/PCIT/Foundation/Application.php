<?php

declare(strict_types=1);

namespace PCIT\Foundation;

use Pimple\Container;

class Application extends Container
{
    public function make($name)
    {
        return $this[$name];
    }
}
