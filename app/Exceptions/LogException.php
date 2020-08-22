<?php

declare(strict_types=1);

namespace App\Exceptions;

use PCIT\Framework\Http\Request;

class LogException extends \Exception
{
    public function render(Request $request)
    {
        // return (new Handler())->render($request,$this);

        return \Response::make('1', 500);
    }
}
