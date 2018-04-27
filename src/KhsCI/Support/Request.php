<?php

declare(strict_types=1);

namespace KhsCI\Support;

class Request
{
    public static function getAllHeaders()
    {
        $headers = [];

        foreach ($_SERVER as $name => $value) {
            if ('HTTP_' === substr($name, 0, 5)) {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

    public static function getHeader($header = null, $default = null)
    {
        $headers = self::getAllHeaders();

        if ($header) {
            return $headers["$header"] ?? $default;
        }

        return $headers;
    }
}
