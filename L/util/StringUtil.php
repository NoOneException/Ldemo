<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/30
 * Time: 19:45
 */

namespace L\util;


class StringUtil
{
    /**
     * 过滤特殊标点
     * @param $str
     * @return string
     */
    public static function filterSymbol(string $str): string
    {
        $str = str_replace([
            '`', '·', '~', '!', '！', '@', '#', '$', '￥', '%', '^', '……', '&', '*', '(', ')', '（', '）', '-', '_', '——',
            '+', '=', '|', '\\', '[', ']', '【', '】', '{', '}', ';', '；', ':', '：', '\'', '"', '“', '”', ',', '，', '<',
            '>', '《', '》', '.', '。', '/', '、', '?', '？',
        ], '', $str);
        return trim($str);
    }

    public static function isEmpty($val): bool
    {
        return $val === null || $val === false || trim($val) === '';
    }

    public static function safeExplode(string $delimiter, string $string): array
    {
        $retArr = [];
        $originArr = explode($delimiter, $string);
        foreach ($originArr as $item) {
            if ($item != '') {
                $retArr[] = $item;
            }
        }
        return $retArr;
    }

    public static function safeDecode(string $json): array
    {
        return $json ? json_decode($json, true) : [];
    }

    public static function equal($str1, $str2): bool
    {
        return (string)$str1 === (string)$str2;
    }

    public static function replaceOnce(string $needle, string $replace, string $haystack): string
    {
        $pos = strpos($haystack, $needle);
        if ($pos === false) {
            return $haystack;
        }
        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }
}