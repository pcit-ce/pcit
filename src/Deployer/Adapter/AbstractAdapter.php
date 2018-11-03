<?php

declare(strict_types=1);

namespace PCIT\Deployer\Adapter;

use PCIT\Deployer\AdapterInterface;

class AbstractAdapter implements AdapterInterface
{
    public function deploy(): void
    {
    }
}
