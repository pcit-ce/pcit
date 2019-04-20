<?php

declare(strict_types=1);

namespace PCIT\Plugin;

interface AdapterInterface
{
    public function deploy(): array;
}
