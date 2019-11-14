<?php

declare(strict_types=1);

namespace PCIT\Framework\Http;

use Symfony\Component\HttpFoundation\Request as RequestBase;

class Request extends RequestBase
{
    public function getAllHeaders()
    {
        if (!\function_exists('getallheaders')) {
            $headers = [];

            foreach ($_SERVER as $name => $value) {
                if ('HTTP_' === substr($name, 0, 5)) {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }

            return $headers;
        }

        return getallheaders();
    }

    public function getHeader($header = null, $default = null)
    {
        $headers = $this->getAllHeaders();

        if ($header) {
            return $headers["$header"] ?? $default;
        }

        return $headers;
    }

    /**
     * @return array
     */
    public function parseLink(string $link = null)
    {
        if (!$link) {
            $link = $this->getHeader('Link');
        } else {
            $link = explode('Link:', $link)[1] ?? null;
        }

        if (!$link) {
            return null;
        }

        $return_array = [];

        foreach (explode(',', $link) as $k) {
            if (preg_match('/https.*[0-9]/', trim($k), $result)) {
                $url = $result[0];
                preg_match('#rel=".*#', trim($k), $result);
                $rel = explode('=', $result[0])[1];
                $return_array[str_replace('"', '', $rel)] = $url;
            }
        }

        return $return_array;
    }
}
