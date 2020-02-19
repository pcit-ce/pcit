<?php

declare(strict_types=1);

namespace PCIT\Framework\Foundation\Http;

class SuccessException extends \Exception
{
    public $code = 200;
}
