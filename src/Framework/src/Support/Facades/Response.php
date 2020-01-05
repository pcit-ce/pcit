<?php

declare(strict_types=1);

namespace PCIT\Framework\Support\Facades;

/**
 * @method static make($content = '', int $status = 200, array $headers = [])
 * @method static json($content, $json)
 * @method static redirect($url)
 * @method static setContent($content)
 * @method static setStatusCode(int $code, $text = null)
 * @method static setHeaders(array $headers=[])
 */
class Response extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'response';
    }
}
