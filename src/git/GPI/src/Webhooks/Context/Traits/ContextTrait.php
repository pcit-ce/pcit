<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Traits;

trait ContextTrait
{
    public function __construct(array $context_array = [], string $raw = '{}')
    {
        parent::__construct($context_array, $raw);

        $this->json_mapper->map(json_decode($raw), $this);

        parent::__construct($context_array, $raw);
    }
}
