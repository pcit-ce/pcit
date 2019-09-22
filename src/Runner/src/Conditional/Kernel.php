<?php

declare(strict_types=1);

namespace PCIT\Builder\Conditional;

use PCIT\Support\Log;

abstract class Kernel
{
    public $conditional;

    public $current;

    public function __construct($conditional, $current)
    {
        $this->conditional = $conditional;
        $this->current = $current;
    }

    /**
     * @return bool true 不跳过
     *
     * @throws \Exception
     */
    public function handle()
    {
        if (!$this->conditional) {
            return true;
        }

        if (\is_string($this->conditional)) {
            $result = $this->stringHandle();
        }

        if (\is_array($this->conditional)) {
            $result = $this->arrayHandle();
        }

        if (false === ($result ?? false)) {
            Log::connect()->emergency(static::class.' conditional not match, skip');
        }

        return $result ?? false;
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    public function regHandle()
    {
        if (!$this->conditional) {
            return true;
        }

        if (\is_string($this->conditional)) {
            $result = $this->stringRegHandle();
        }

        if (\is_array($this->conditional)) {
            $result = $this->arrayRegHandle();
        }

        if (false === ($result ?? false)) {
            Log::connect()->emergency(static::class.' conditional not match, skip');
        }

        return $result ?? false;
    }

    /**
     * @return bool
     */
    protected function stringHandle()
    {
        if ($this->current === $this->conditional) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function arrayHandle()
    {
        if ((\in_array($this->current, $this->conditional, true))) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function stringRegHandle()
    {
        return 1 === preg_match("#$this->conditional#", $this->current);
    }

    /**
     * @return bool
     */
    public function arrayRegHandle()
    {
        $result = $this->conditional;

        foreach ($result as $k) {
            $this->conditional = $k;
            $result = $this->stringRegHandle();

            if ($result) {
                return true;
            }
        }

        return false;
    }
}
