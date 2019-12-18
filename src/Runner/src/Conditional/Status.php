<?php

declare(strict_types=1);

namespace PCIT\Runner\Conditional;

class Status
{
    /**
     * @param string|array|null $status
     * @param string            $target
     *
     * @return bool
     */
    public function handle($status, $target)
    {
        if (!$status) {
            return false;
        }

        if (\is_string($status)) {
            if (\in_array($status, ['failure', 'success', 'changed'], true)) {
                return $status === $target;
            }
        }

        if (\is_array($status)) {
            foreach ($status as $k) {
                if ($k === $target) {
                    return true;
                }
            }
        }

        return false;
    }
}
