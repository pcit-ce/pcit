<?php

declare(strict_types=1);

namespace App;

use PCIT\Framework\Support\Model;

class Cron extends Model
{
    protected static $table = 'cron';

    public static function list(): void
    {
    }
}
