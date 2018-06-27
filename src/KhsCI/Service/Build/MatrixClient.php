<?php

declare(strict_types=1);

namespace KhsCI\Service\Build;

use KhsCI\Support\ArrayHelper;

class MatrixClient
{
    /**
     * 解析矩阵.
     *
     * @param array $matrix
     *
     * @return array
     */
    public static function parseMatrix(array $matrix)
    {
        return ArrayHelper::combination($matrix);
    }
}
