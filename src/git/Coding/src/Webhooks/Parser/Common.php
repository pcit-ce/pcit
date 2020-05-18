<?php

declare(strict_types=1);

namespace PCIT\Coding\Webhooks\Parser;

trait Common
{
    public static function handle_repo_full_name($repo_full_name)
    {
        $repo_full_name_array = explode('/', $repo_full_name);
        array_shift($repo_full_name_array);

        return implode('/', $repo_full_name_array);
    }
}
