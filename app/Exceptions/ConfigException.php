<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Build;

class ConfigException extends \Exception
{
    public $build_key_id;

    public function report(): void
    {
        Build::updateBuildStatus(
            (int) $this->build_key_id,
            $this->message
        );
    }
}
