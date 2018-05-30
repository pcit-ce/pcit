<?php

declare(strict_types=1);

namespace App\Cron;

use KhsCI\Support\DBModel;

class Cron extends DBModel
{
    protected $table = 'cron';

    public static function list(): void
    {
    }
}
