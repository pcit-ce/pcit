<?php

declare(strict_types=1);

namespace PCIT\Framework\Foundation\Http;

use PCIT\Framework\Support\Response;
use Route;
use Throwable;

class Kernel
{
    private function sendRequestThroughRouter($request)
    {
        $debug = config('app.debug');

        if (!\defined('PCIT_START')) {
            \define('PCIT_START', 0);
        }

        // ci.khs1994.com/index.php redirect to /dashboard

        // if ('/index.php' === $_SERVER['REQUEST_URI']) {
        if ('/index.php' === $request->server->get('REQUEST_URI')) {
            Response::redirect('dashboard');

            exit;
        }

        // 引入路由文件
        try {
            require_once base_path().'framework/routes/web.php';
        } catch (Throwable $e) {
            if ('Finish' === $e->getMessage()) {
                $output = Route::getOutput();

                if ($output instanceof Response) {
                    return $output;
                }

                switch (\gettype($output)) {
                    case 'array':
                        return Response::json(
                            array_merge(
                                $output, ['code' => $e->getCode()]
                            ), PCIT_START);

                        break;
                    case 'integer':
                       if ('testing' === env('APP_ENV')) {
                           return $output;
                       }

                        return new Response($output);

                        break;
                    case 'string':
                        if ('testing' === env('APP_ENV')) {
                            return $output;
                        }

                        return new Response($output);

                        break;
                }

                return new Response();
            }

            method_exists($e, 'report') && $e->report($e);
            method_exists($e, 'render') && $e->render($request, $e);

            return Response::json(array_filter([
                'code' => 500,
                'message' => $e->getMessage() ?: 'ERROR',
                'documentation_url' => 'https://github.com/pcit-ce/pcit/tree/master/docs/api',
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
            'obj' => $debug ? Route::getObj() ?? null : null,
            'method' => $debug ? Route::getMethod() ?? null : null,
            'details' => $debug ? '路由控制器填写错误' : null,
        ]), PCIT_START);
    }

    public function handle($request)
    {
        try {
            ini_set('session.cookie_path', '/');
            ini_set('session.cookie_domain', '.'.getenv('CI_SESSION_DOMAIN'));
            ini_set('session.gc_maxlifetime', '690000'); // s
            ini_set('session.cookie_lifetime', '690000'); // s
            ini_set('session.cookie_secure', 'On');
        } catch (Throwable $e) {
        }

        app()->instance('request', $request);

        // session_set_cookie_params(1800 , '/', '.'getenv('CI_SESSION_DOMAIN', true));

        $response = $this->sendRequestThroughRouter($request);

        return $response;
    }
}
