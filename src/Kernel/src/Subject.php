<?php

declare(strict_types=1);

namespace PCIT;

/**
 * 观察者模式 主题.
 */
class Subject
{
    private $observers = [];

    /**
     * @var array
     */
    public $config_array = [];

    public function register($observer)
    {
        $this->observers[] = $observer;

        return $this;
    }

    public function handle()
    {
        try {
            foreach ($this->observers as $obj) {
                if ($obj instanceof GetConfig) {
                    $this->config_array = $obj->handle();

                    continue;
                }

                $obj->handle();
            }
        } catch (\Throwable $e) {
            $this->observers = [];

            throw $e;
        }

        $this->observers = [];

        return $this;
    }
}
