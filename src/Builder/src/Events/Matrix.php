<?php

declare(strict_types=1);

namespace PCIT\Builder\Events;

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
        if ($matrix['include'] ?? false) {
            return $matrix['include'];
        }

        if (!$matrix) {
            return [];
        }

        return ArrayHelper::combination($matrix);
    }
}
