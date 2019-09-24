<?php

declare(strict_types=1);

if (!function_exists('pcit')) {
    function pcit()
    {
        return app(PCIT\PCIT::class);
    }
}
