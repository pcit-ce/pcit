<?php

declare(strict_types=1);

namespace Tests;

trait CreatesApplication
{
    public function createApplication()
    {
        putenv('APP_ENV=testing');

        $app = require __DIR__.'/../framework/bootstrap/app.php';

        return $app;
    }
}
