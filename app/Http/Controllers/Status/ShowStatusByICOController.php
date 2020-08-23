<?php

declare(strict_types=1);

namespace App\Http\Controllers\Status;

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
     * @throws \Exception
     */
    public function __call(string $status, array $param)
    {
        var_dump($param);
        exit;
        $svg = 'public/ico/unknown.svg';

        try {
            $file = 'public/ico/' . $status . '.svg';

            if (file_exists(base_path($file))) {
                $svg = $file;
            }
        } catch (\Throwable $e) {
        }

        return \Response::file(base_path($svg), [
            'content-type' => 'image/svg+xml;charset=utf-8',
            'Cache-Control' => 'max-age=300',
            // header('Cache-Control: max-age=100');
            // no-cache
        ]);
    }
}
