<?php

declare(strict_types=1);

namespace PCIT\Deployer;

interface AdapterInterface
{
    public function deploy(): array;
}
