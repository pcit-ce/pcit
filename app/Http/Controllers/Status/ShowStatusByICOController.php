<?php

declare(strict_types=1);

namespace App\Http\Controllers\Status;

use Error;
use Exception;

/**
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
    public function __call(string $k, array $v): void
    {
        try {
            header('content-type: image/svg+xml;charset=utf-8');
            header('Cache-Control: no-cache');
            // header('Cache-Control: max-age=100');
            $file = __DIR__.'/../../../../public/ico/'.$k.'.svg';

            if (file_exists($file)) {
                return file_get_contents($file);
            }
        } catch (Error $e) {
            return file_get_contents(__DIR__.'/../../../../public/ico/unknown.svg');
        }
    }
}
