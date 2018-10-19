<?php

declare(strict_types=1);

namespace PCIT\Service\Build\Events;

use PCIT\Support\ArrayHelper;

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
