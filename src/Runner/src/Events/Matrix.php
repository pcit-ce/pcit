<?php

declare(strict_types=1);

namespace PCIT\Runner\Events;

use PCIT\Framework\Support\ArrayHelper;

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
            $matrix_array = [];

            foreach ($matrix['include'] as $item) {
                $matrix_array[] = (array) $item;
            }

            return $matrix_array;
        }

        if (!$matrix) {
            return [];
        }

        return ArrayHelper::combination($matrix);
    }
}
