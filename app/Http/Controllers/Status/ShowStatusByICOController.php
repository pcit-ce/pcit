<?php

declare(strict_types=1);

namespace App\Http\Controllers\Status;

use Error;
use Exception;
use PCIT\Framework\Support\Response;

/**
 * 获取状态小图标.
 *
 * @method static canceled()
 * @method static errored()
 * @method static failing()
 * @method static passing()
 * @method static pending()
 */
class ShowStatusByICOController
{
    /**
     * @param string $k
     * @param array  $v
     *
     * @throws Exception
     */
    public function __call(string $k, array $v)
    {
        try {
            $file = __DIR__.'/../../../../public/ico/'.$k.'.svg';

            if (file_exists($file)) {
                $svg = file_get_contents($file);
            }
        } catch (Error $e) {
            $svg = file_get_contents(__DIR__.'/../../../../public/ico/unknown.svg');
        }

        return new Response($svg, 200, [
            'content-type' => 'image/svg+xml;charset=utf-8',
            'Cache-Control' => 'max-age=300',
            // header('Cache-Control: max-age=100');
            // no-cache
        ]);
    }
}
