<?php

declare(strict_types=1);

namespace PCIT\Plugin;

interface PluginInterface
{
    public function deploy(): array;
}
