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
            $file = __DIR__.'/../../../../public/ico/'.$k.'.svg';

            if (file_exists($file)) {
                require $file;

                exit();
            }

            throw new Exception();

        } catch (Error $e) {
            require __DIR__.'/../../../../public/ico/unknown.svg';
        }
    }
}
