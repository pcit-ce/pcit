<?php

declare(strict_types=1);

namespace PCIT\Framework\Support;

class ArrayHelper
{
    /**
     * 实现二维数组的组合.
     *
     * @return array
     *
     * @see https://www.zhihu.com/question/35599231
     */
    public static function combination(array $options)
    {
        $rows = [];

        foreach ($options as $option => $items) {
            if (\count($rows) > 0) {
                // 2、将第一列作为模板
                $clone = $rows;

                // 3、置空当前列表，因为只有第一列的数据，组合是不完整的
                $rows = [];

                // 4、遍历当前列，追加到模板中，使模板中的组合变得完整
                foreach ($items as $item) {
                    $tmp = $clone;
                    foreach ($tmp as $index => $value) {
                        $value[$option] = $item;
                        $tmp[$index] = $value;
                    }

                    // 5、将完整的组合拼回原列表中
                    $rows = array_merge($rows, $tmp);
                }
            } else {
                // 1、先计算出第一列
                foreach ($items as $item) {
                    $rows[][$option] = $item;
                }
            }
        }

        return $rows;
    }

    public static function compare(array $arr1, array $arr2): bool
    {
        ksort($arr1);
        ksort($arr2);

        return $arr1 === $arr2;
    }
}
