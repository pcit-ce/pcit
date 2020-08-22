<?php

declare(strict_types=1);

namespace PCIT\Framework\Foundation\Http;

use PCIT\Framework\Routing\Exceptions\SuccessHandleRouteException;
use ReflectionClass;
use Route;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Throwable;

class Kernel
{
    private function getControllers(): array
    {
        $cache_path = base_path().'framework/storage/controllers.cache.php';

        if (file_exists($cache_path)) {
            $controllers = require $cache_path;

            if ($controllers) {
                return $controllers;
            }
        }

        $controllers = [];

        $finder = Finder::create()
            ->in(base_path().'app/Http/Controllers')
            ->name('*.php')
            ->files();

        foreach ($finder as $item) {
            $controller = explode('.', $item->getRelativePathname())[0];
            $controllers[] = str_replace('/', '\\', $controller);
        }

        file_put_contents($cache_path, '<?php return '.json_encode($controllers).';');

        return $controllers;
    }

    private function sendRequestThroughRouterByAttributes(): void
    {
        $controllers = $this->getControllers();

        foreach ($controllers as $controller) {
            $class = new ReflectionClass('\\App\\Http\Controllers\\'.$controller);

            $methods = $class->getMethods();

            foreach ($methods as $method) {
                $attrs = $method->getAttributes();
                foreach ($attrs as $attr) {
                    if ('Route' !== $attr->getName()) {
                        continue;
                    }

                    // $attr->newInstance();

                    $controller_method = null;
                    if ('__invoke' !== $method->getName()) {
                        $controller_method = '@'.$method->getName();
                    }

                    (new ReflectionClass($attr->getName()))->newInstance(...[
                        ...$attr->getArguments(),
                        $controller.$controller_method,
                    ]);
                }
            }
        }
    }

    private function sendRequestThroughRouter($request)
    {
        $debug = config('app.debug');

        if (!\defined('PCIT_START')) {
            \define('PCIT_START', 0);
        }

        // ci.khs1994.com/index.php redirect to /dashboard

        // if ('/index.php' === $_SERVER['REQUEST_URI']) {
        if ('/index.php' === $request->server->get('REQUEST_URI')) {
            \Response::redirect('dashboard');

            exit;
        }

        // 引入路由文件
        try {
            // if(explode('/',$request->server->get('REQUEST_URI'))[1] === 'api'){
            //     require base_path().'framework/routes/api.php';
            // }else{

            $this->sendRequestThroughRouterByAttributes();

            require base_path().'framework/routes/web.php';
            // }
        } catch (Throwable $e) {
            if ($e instanceof SuccessHandleRouteException) {
                $output = Route::getResponse();

                if ($output instanceof HttpFoundationResponse) {
                    return $output;
                }

                switch (\gettype($output)) {
                    case 'array':
                        return \Response::json(
                            array_merge(
                                $output,
                                ['code' => $e->getCode()]
                            )
                        );

                        break;
                    case 'integer':
                       if ('testing' === config('app.env')) {
                           return $output;
                       }

                        return \Response::make((string) $output);

                        break;
                    case 'string':
                        if ('testing' === config('app.env')) {
                            return $output;
                        }

                        return \Response::make($output);

                        break;
                }

                return \Response::make();
            }

            // 出现错误
            method_exists($e, 'report') && $e->report($e);
            method_exists($e, 'render') && $e->render($request, $e);

            $previousErr = $e->getPrevious();
            // var_dump($previousErr);

            $errDetails['trace'] = $previousErr ? $previousErr->getTrace() : $e->getTrace();

            return \Response::json(array_filter([
                'code' => $e->getCode() ?: 500,
                'message' => $e->getMessage() ?: 'ERROR',
                'documentation_url' => 'https://github.com/pcit-ce/pcit/tree/master/docs/api',
                'file' => $debug ? ($previousErr ? $previousErr->getFile() : $e->getFile()) : null,
                'line' => $debug ? ($previousErr ? $previousErr->getLine() : $e->getLine()) : null,
                'details' => $debug ? $errDetails : null,
            ]));
        }

        // 路由控制器填写错误
        return \Response::json(array_filter([
            'code' => 404,
            'message' => 'Not Found',
            'api_url' => config('app.host').'/api',
            'obj' => $debug ? Route::getObj() ?? null : null,
            'method' => $debug ? Route::getMethod() ?? null : null,
            'details' => $debug ? '路由控制器填写错误' : null,
        ]));
    }

    public function handle($request)
    {
        try {
            ini_set('session.cookie_path', '/');
            ini_set('session.cookie_domain', config('session.domain'));
            ini_set('session.gc_maxlifetime', '690000'); // s
            ini_set('session.cookie_lifetime', '690000'); // s
            // ini_set('session.cookie_secure', 'On');
        } catch (Throwable $e) {
        }

        app()->instance('request', $request);
        app()->instance(\PCIT\Framework\Http\Request::class, $request);

        // session_set_cookie_params());

        return $this->sendRequestThroughRouter($request);
    }
}
