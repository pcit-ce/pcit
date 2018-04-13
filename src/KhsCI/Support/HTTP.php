<?php

declare(strict_types=1);

namespace KhsCI\Support;

class HTTP
{
    public static function getAllHeaders()
    {
        if (!function_exists('getallheaders')) {
            function getallheaders()
            {
                $headers = [];
                foreach ($_SERVER as $name => $value) {
                    if ('HTTP_' === substr($name, 0, 5)) {
                        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                    }
                }

                return $headers;
            }
        }

        return \getallheaders();
    }
}
