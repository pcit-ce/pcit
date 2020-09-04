<?php

declare(strict_types=1);

namespace PCIT\Framework\Support\Facades;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @method static \PCIT\Framework\Http\Response make($content = '', int $status = 200, array $headers = [])
 * @method static \Symfony\Component\HttpFoundation\Response json($content, $json)
 * @method static void redirect($url)
 * @method static \PCIT\Framework\Http\Response setContent(?string $content)
 * @method static \PCIT\Framework\Http\Response setStatusCode(int $code, $text = null)
 * @method static \PCIT\Framework\Http\Response setHeaders(array $headers=[])
 * @method static ResponseHeaderBag             headers()
 * @method static \Symfony\Component\HttpFoundation\BinaryFileResponse download(\SplFileInfo|string $file, ?string $name = null, array $headers = [], null|string $disposition = 'attachment')
 * @method static \Symfony\Component\HttpFoundation\BinaryFileResponse file($file, array $headers = [])
 * @method static \Illuminate\Http\Response noContent($status = 204, array $headers = [])
 */
class Response extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'response';
    }
}
