<?php

declare(strict_types=1);

namespace PCIT\Runner\Conditional;

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
     * @param bool $reg conditional is reg
     *
     * @return bool true 不跳过
     *
     * @throws \Exception
     */
    public function handle(bool $reg = false): bool
    {
        if (!$this->conditional) {
            return true;
        }

        // branch: dev
        if (\is_string($this->conditional)) {
            $result = $reg ? $this->stringRegHandle() : $this->stringHandle();
        }

        // branch: ["dev"]
        if (\is_array($this->conditional)) {
            $result = $reg ? $this->arrayRegHandle() : $this->arrayHandle();
        }

        // branch:
        //   include: dev
        if (\is_object($this->conditional)) {
            $include = $this->conditional->include ?? [];
            $exclude = $this->conditional->exclude ?? [];

            if ($exclude) {
                $this->conditional = $exclude;
                $result = $this->handle($reg);

                if (true === ($result ?? false)) {
                    return false;
                } else {
                    return true;
                }
            }

            if ($include) {
                $this->conditional = $include;
                $result = $this->handle($reg);

                if (true === ($result ?? false)) {
                    return true;
                }
            }
        }

        if (false === ($result ?? false)) {
            \Log::emergency(static::class.' conditional not match, skip');
        }

        return $result ?? false;
    }

    /**
     * @return bool
     */
    public function stringHandle()
    {
        if ($this->current === $this->conditional) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function arrayHandle()
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
