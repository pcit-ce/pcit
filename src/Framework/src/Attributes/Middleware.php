<?php

declare(strict_types=1);

namespace PCIT\Framework\Attributes;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
class Middleware
{
    public function __construct($middleware)
    {
        $middleware_class = 'App\\Http\\Middleware\\'.$middleware;

        $rc = new \ReflectionClass($middleware_class);

        $this->middleware = $rc->newInstanceArgs($this->getParameters($rc->getName()));
    }

    public function getParameters($obj, $method = '__construct'): array
    {
        try {
            $rm = new \ReflectionMethod($obj, $method);
        } catch (\Throwable $e) {
            return [];
        }

        $parameters = [];

        $rm_parameters = $rm->getParameters();

        foreach ($rm_parameters as $parameter) {
            if (!$parameter->hasType()) {
                throw new \Exception('Middleware constuctor parameters must have type');
            }

            try {
                $instance = app($parameter->getType()->getName());

                $parameters[] = $instance;
            } catch (\Throwable $e) {
                // 类型提示未在容器中绑定
                $parameter_rc = new \ReflectionClass($parameter->getType()->getName());

                $parameters[] = $parameter_rc->newInstanceArgs($this->getParameters($parameter_rc->getName()));
            }
        }

        return $parameters;
    }
}
