<?php

declare(strict_types=1);

namespace PCIT\Framework\Support;

class Subject
{
    public $observers = [];

    public function register($observer)
    {
        $this->observers[] = $observer;

        return $this;
    }

    public function handle(): void
    {
        foreach ($this->observers as $instance) {
            $instance->handle();
        }

        $this->observers = [];
    }
}
