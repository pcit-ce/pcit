<?php

declare(strict_types=1);

namespace PCIT\Plugin\Adapter;

use PCIT\Plugin\AdapterInterface;

class AbstractAdapter implements AdapterInterface
{
    public function deploy(): array
    {
        return ['image' => '', 'env' => []];
    }
}
