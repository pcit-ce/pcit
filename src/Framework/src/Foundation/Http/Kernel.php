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
        $cache_path = base_path('framework/storage/controllers.cache.php');

        if (file_exists($cache_path)) {
            $controllers = require $cache_path;

            if ($controllers) {
                return $controllers;
            }
        }

        $controllers = [];

        $finder = Finder::create()
            ->in(base_path('app/Http/Controllers'))
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
                    if (\PCIT\Framework\Attributes\Route::class !== $attr->getName()) {
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

    public function convertToResponse($response): HttpFoundationResponse
    {
        if ($response instanceof HttpFoundationResponse) {
            return $response;
        }

        switch (\gettype($response)) {
            case 'array':
                return \Response::json(
                    array_merge(
                        $response,
                        ['code' => 200]
                    )
                );

            case 'integer':
                return \Response::make((string) $response);
            case 'float':
                return \Response::make((string) $response);
            case 'string':
                return \Response::make((string) $response);
        }

        return \Response::make($response);
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
            //     require base_path('framework/routes/api.php');
            // }else{

            $this->sendRequestThroughRouterByAttributes();

            require base_path('framework/routes/web.php');
            // }
        } catch (Throwable $e) {
            if ($e instanceof SuccessHandleRouteException) {
                $response = Route::getResponse();

                return $this->convertToResponse($response)->prepare($request);
            }

            $code = (int) $e->getCode();

            if (HttpFoundationResponse::$statusTexts[$code] ?? false) {
            } else {
                $code = 500;
            }

            $exceptionHandler = new \App\Exceptions\Handler();

            // 出现错误
            if (method_exists($e, 'report')) {
                $e->report();
            } else {
                $exceptionHandler->report($e);
            }

            if (method_exists($e, 'render')) {
                return $this->convertToResponse($e->render($request));
            }

            return $exceptionHandler->render($request, $e);
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
