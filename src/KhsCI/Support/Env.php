<?php

declare(strict_types=1);

namespace KhsCI\Support;

class Env
{
    /**
     * @param string      $key
     * @param string|null $default
     *
     * @return array|false|string
     */
    public static function get(string $key, string $default = null)
    {
        try {
            $value = getenv($key);

            if (false === $value) {
                $value = $default;
            }
        } catch (\Exception $e) {
            $value = $default;
        }

        return $value;
    }
}
