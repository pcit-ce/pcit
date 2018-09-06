<?php

declare(strict_types=1);

namespace App\Console\Events;

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
        foreach ($this->observers as $obj) {
            $obj->handle();
        }

        $this->observers = [];
    }
}
