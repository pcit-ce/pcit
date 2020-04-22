<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks;

class Context implements ContextInterface
{
    public $raw;
    public $context_array;

    public function __construct(array $context_array, string $raw)
    {
        $this->raw = $raw;
        $this->context_array = $context_array;
    }

    public function __get(string $name)
    {
        return $this->context_array[$name];
    }
}
