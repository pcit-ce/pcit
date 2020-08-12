<?php

declare(strict_types=1);

if (!function_exists('pcit')) {
    function pcit(): PCIT\PCIT
    {
        return app(PCIT\PCIT::class);
    }
}
