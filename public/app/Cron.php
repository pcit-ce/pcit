<?php

declare(strict_types=1);

namespace App;

use PCIT\Support\DBModel;

class Cron extends DBModel
{
    protected static $table = 'cron';

    public static function list(): void
    {
    }
}
