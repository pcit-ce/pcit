<?php

declare(strict_types=1);

namespace PCIT\Framework\Http;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request as RequestBase;

/**
 * @method InputBag  cookies()
 * @method HeaderBag headers()
 */
class Request extends RequestBase
{
    // if (!\function_exists('getallheaders')) {
    //     $headers = [];

    //     foreach ($_SERVER as $name => $value) {
    //         if ('HTTP_' === substr($name, 0, 5)) {
    //             $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
    //         }
    //     }

    //     return $headers;
    // }

    public function __call(string $method, array $args)
    {
        return $this->$method;
    }

    /**
     * @return array
     */
    public function parseLink(string $link = null)
    {
        if (!$link) {
            $link = $this->headers()->get('Link');
        } else {
            $link = explode('Link:', $link)[1] ?? null;
        }

        if (!$link) {
            return;
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
