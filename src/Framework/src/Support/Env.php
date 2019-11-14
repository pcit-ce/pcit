<?php

declare(strict_types=1);

namespace PCIT\Framework\Support;

class Env
{
    /**
     * @param string|int $default
     *
     * @return array|false|string
     */
    public static function get(string $key, $default = null)
    {
        try {
            $value = getenv($key);

            if (false === $value or '' === $value) {
                $value = $default;
            }

            'false' === $value && $value = false;
            'true' === $value && $value = true;
        } catch (\Exception $e) {
            $value = $default;
        }

        return $value;
    }
}
