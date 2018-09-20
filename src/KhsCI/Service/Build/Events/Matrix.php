<?php

declare(strict_types=1);

namespace KhsCI\Service\Build\Events;

use KhsCI\Support\ArrayHelper;

class Matrix
{
    /**
     * 解析矩阵.
     *
     * @param array $matrix
     *
     * @return array
     */
    public static function parseMatrix(?array $matrix)
    {
        if (!$matrix) {
            return [];
        }

        return ArrayHelper::combination($matrix);
    }
}
