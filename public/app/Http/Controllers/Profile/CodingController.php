<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\Session;

class CodingController extends GitHubController
{

    protected $git_type = 'coding';
}
