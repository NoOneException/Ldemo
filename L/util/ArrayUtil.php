<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/10/9
 * Time: 17:02
 */

namespace L\util;


class ArrayUtil
{
    public static function arrayColumn(array $array, string $columnKey = 'id', string $indexKey = ''): array
    {
        if (function_exists('array_column ')) {
            return array_column($array, $columnKey, $indexKey);
        }
        $result = [];
        foreach ($array as $arr) {
            if (!is_array($arr)) {
                continue;
            }
            if (is_null($columnKey)) {
                $value = $arr;
            } else {
                $value = $arr[$columnKey];
            }

            if (!is_null($indexKey)) {
                $key = $arr[$indexKey];
                $result[$key] = $value;
            } else {
                $result[] = $value;
            }
        }
        return $result;
    }

    /**
     * 把二维数组中的id填充到索引
     * @param array $array
     * @param string $idKey
     * @param bool $isFilterEmpty
     * @param string $sep
     * @return array
     */
    public static function arrayFillKey(array $array, string $idKey = 'id', bool $isFilterEmpty = true, string $sep = '_'): array
    {
        $arr = [];
        $keyArr = explode(',', $idKey);
        foreach ($array as $item) {
            $keyValArr = [];
            $hasEmpty = false;
            foreach ($keyArr as $key) {
                if ($isFilterEmpty) {
                    if (StringUtil::isEmpty($item[$key])) {
                        $hasEmpty = true;
                        break;
                    }
                }
                $keyValArr[] = $item[$key];
            }
            if ($hasEmpty) {
                continue;
            }
            $arr[implode($sep, $keyValArr)] = $item;
        }
        return $arr;
    }
}