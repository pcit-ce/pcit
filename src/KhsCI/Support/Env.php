<?php

namespace KhsCI\Support;

class Env
{
    public static function get($key, $default = null)
    {
        try {
            $value = getenv($key);
        } catch (\Exception $e) {
            $value = $default;
        }

        return $value;
    }
}
