<?php

declare(strict_types=1);

namespace PCIT\Foundation\Http;

use PCIT\Support\Env;
use PCIT\Support\Response;
use PCIT\Support\Route;
use Throwable;

class Kernel
{
    private function sendRequestThroughRouter()
    {
        $debug = true === Env::get('CI_DEBUG', false);

        if ('/index.php' === $_SERVER['REQUEST_URI']) {
            Response::redirect('dashboard');
            exit;
        }

        try {
            require_once base_path().'framework/routes/web.php';
        } catch (Throwable $e) {
            if ('Finish' === $e->getMessage()) {
                $output = Route::$output;

                switch (\gettype($output)) {
                    case 'array':
                        return Response::json($output, PCIT_START);

                        break;
                    case 'integer':
                        echo $output;

                        break;
                    case 'string':
                        echo $output;

                        break;
                }
                exit;
            }

            return Response::json(array_filter([
                'code' => $e->getCode() ?? 500,
                'message' => $e->getMessage() ?? 'ERROR',
                'documentation_url' => 'https://github.com/khs1994-php/pcit/tree/master/docs/api',
                'file' => $debug ? $e->getFile() : null,
                'line' => $debug ? $e->getLine() : null,
                'details' => $debug ? (array) $e->getPrevious() : null,
            ]), PCIT_START);
        }

        // 路由控制器填写错误
        return Response::json(array_filter([
            'code' => 404,
            'message' => 'Not Found',
            'api_url' => getenv('CI_HOST').'/api',
            'obj' => $debug ? Route::$obj ?? null : null,
            'method' => $debug ? Route::$method ?? null : null,
            'details' => $debug ? '路由控制器填写错误' : null,
        ]), PCIT_START);
    }

    public function handle($requests)
    {
        ini_set('session.cookie_path', '/');
        ini_set('session.cookie_domain', '.'.getenv('CI_SESSION_DOMAIN'));
        ini_set('session.gc_maxlifetime', '690000'); // s
        ini_set('session.cookie_lifetime', '690000'); // s
        ini_set('session.cookie_secure', 'On');

        // session_set_cookie_params(1800 , '/', '.'getenv('CI_SESSION_DOMAIN', true));

        $response = $this->sendRequestThroughRouter();

        return $response;
    }
}
