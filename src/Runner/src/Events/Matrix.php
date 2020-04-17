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
     * @return array [['k1'=>'v1'],['k1'=>'v2']]
     */
    public static function handle(?array $matrix)
    {
        if (!$matrix) {
            return [];
        }

        // var_dump($matrix);

        $include_matrix_array = [];

        if ($matrix['include'] ?? false) {
            foreach ($matrix['include'] as $item) {
                $include_matrix_array[] = (array) $item;
            }

            if (1 === \count($matrix)) {
                return $include_matrix_array;
            }
        }

        $matrix_array = array_merge(
            ArrayHelper::combination($matrix),
            $include_matrix_array
        );

        // 不包含 exclude 返回
        if (!($matrix['exclude'] ?? false)) {
            return $matrix_array;
        }

        $exclude_matrix_array = [];
        foreach ($matrix['exclude'] as $item) {
            $exclude_matrix_array[] = (array) $item;
        }

        return array_values(
            array_filter(
                $matrix_array,
                function ($item) use ($exclude_matrix_array) {
                    if (!\in_array($item, $exclude_matrix_array)) {
                        return true;
                    }
                }
            )
        );
    }
}
