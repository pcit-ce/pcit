<?php

declare(strict_types=1);

namespace App\Exceptions;

use PCIT\Framework\Http\Request;

class Exception extends \Exception
{
    public function report(): void
    {
    }

    public function render(Request $request): void
    {
    }
}
